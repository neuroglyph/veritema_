using System;
using System.Linq;
using System.Net.Http;
using System.Threading.Tasks;
using System.Web.Http;
using System.Web.Http.Results;
using Veritema.Data;
using Veritema.Eventing;
using Veritema.Security;
using VeritemaMVC_3.Models;

namespace Veritema.Controllers
{
    [RoutePrefix("api/schedule")]
    //[HmacAuthentication]
    //[ApiKeyAuthentication]
    public class ScheduleApiController : ApiController
    {
        private readonly IEventLoader _eventLoader;
        private readonly IEventScrubber _eventScrubber;
        private readonly IEventWriter _eventWriter;
        private readonly IEventPublisher _eventPublisher;

        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleApiController"/> class.
        /// </summary>
        /// <param name="eventLoader">The event loader.</param>
        /// <param name="eventWriter">The event writer.</param>
        /// <param name="eventScrubber">The event scrubber.</param>
        /// <param name="eventPublisher">The event publisher.</param>
        /// <exception cref="System.ArgumentNullException">
        /// eventWriter
        /// or
        /// eventScrubber
        /// or
        /// eventLoader
        /// </exception>
        public ScheduleApiController(IEventLoader eventLoader, IEventWriter eventWriter, IEventScrubber eventScrubber, IEventPublisher eventPublisher)
        {
            if (eventWriter == null)
            {
                throw new ArgumentNullException(nameof(eventWriter));
            }
            if (eventScrubber == null)
            {
                throw new ArgumentNullException(nameof(eventScrubber));
            }
            if (eventLoader == null)
            {
                throw new ArgumentNullException(nameof(eventLoader));
            }
            if (eventPublisher == null)
            {
                throw new ArgumentNullException(nameof(eventPublisher));
            }
            _eventLoader = eventLoader;
            _eventWriter = eventWriter;
            _eventScrubber = eventScrubber;
            _eventPublisher = eventPublisher;
        }


        /// <summary>
        /// Deletes the event.
        /// </summary>
        /// <param name="id">The event identifier.</param>
        /// <returns>The Id of the event to be deleted.</returns>
        [Route("{id}")]
        [HttpDelete]
        public async Task<IHttpActionResult> DeleteEventAsync(string id)
        {
            await _eventScrubber.DeleteAsync(Int64.Parse(id));
            return new ResponseMessageResult(Request.CreateResponse(System.Net.HttpStatusCode.Accepted, id));
        }

        /// <summary>
        /// Retrieve the defined events
        /// </summary>
        /// <returns>ActionResult.</returns>
        [Route("events")]
        [HttpGet]
        public async Task<IHttpActionResult> GetEventsAsync([FromUri]string from, [FromUri]string to)
        {
            var events = await _eventLoader.GetAsync(from: MapDynamicDate(from), to: MapDynamicDate(to));
            return Json(events.Select(i => Map(i)).ToArray());
        }

        [HttpPost]
        [Route("{id}")]
        public async Task<IHttpActionResult> NewEventAsync(string id, ScheduleEvent @event)
        {
            @event.id = -1;
            Event updated = await _eventWriter.CreateAsync(Map(@event));
            await Publish(_eventPublisher.OnCreatedAsync, updated);
            return Ok(Map(updated));
        }

        /// <summary>
        /// Maps an <see cref="Event"/> to a <see cref="ScheduleEvent"/>.
        /// </summary>
        /// <param name="event">The event to map.</param>
        /// <returns>the representative ScheduleEvent.</returns>
        private static ScheduleEvent Map(Event @event) => new ScheduleEvent
        {
            id = @event.Id,
            text = @event.Description,
            title = @event.Title ?? "Private Lesson",
            start_date = @event.StartUtc.ToLocalTime().ToString(ScheduleEvent.DateTimeFormat),
            end_date = @event.EndUtc.ToLocalTime().ToString(ScheduleEvent.DateTimeFormat),
            event_location_id = @event?.Location != null ? @event.Location.Id.ToString() : "1",
            event_location = new ScheduleLocation
            {
                id = @event.Location.Id.ToString(),
                city = @event.Location.City,
                name = @event.Location.Name,
                //phone = @event.Location.Phone,
                state = @event.Location.State,
                street = @event.Location.Street,
                street2 = @event.Location.Street2,
                zip = @event.Location.Zip
            },
            type = @event.Type.ToString()
        };

        private static Event Map(ScheduleEvent @event)
        {


            // 10.27.2016 RG - thinking of some kind of boolean to determine what to show
            //                 based on user type, event type, etc
            bool editable = false;


            Event e = new Event
            {
                Id = @event.id,
                Description = @event.text,
                EndUtc = DateTime.ParseExact(@event.end_date, ScheduleEvent.DateTimeFormat, null),
                StartUtc = DateTime.ParseExact(@event.start_date, ScheduleEvent.DateTimeFormat, null),
                Title = @event.title,
            };

            MartialArtStyle style;

            if (Enum.TryParse(@event.style, out style))
            {
                e.Style = style;
            }

            switch (@event.type)
            {
                case "S":
                    e.Type = EventType.Seminar;
                    break;
                case "P":
                    e.Type = EventType.PrivateLesson;
                    break;
                case "C":
                default:
                    e.Type = EventType.Class;
                    break;
            }


            if (@event.event_location != null)
            {
                e.Location = new Location
                {
                    City = @event.event_location.city,
                    Name = @event.event_location.name,
                    //Phone = @event.event_location.phone,
                    State = @event.event_location.state,
                    Street = @event.event_location.street,
                    Street2 = @event.event_location.street2,
                    Zip = @event.event_location.zip
                };
            }

            int id;
            if (int.TryParse(@event.event_location_id, out id))
            {
                if (e.Location == null) e.Location = new Location();
                e.Location.Id = id;
            }

            return e;
        }

        private static DateTime? MapDynamicDate(string date)
        {
            DateTime? parsed = new DateTime?();
            DateTime dt;
            if (DateTime.TryParse(date, out dt))
            {
                parsed = dt;
            }
            return parsed;
        }

        /// <summary>
        /// Attempt to publish an event
        /// </summary>
        /// <param name="action">The publish action.</param>
        /// <param name="event">The event to be published.</param>
        /// <returns>The <see cref="Task"/> orchestrating the action.</returns>
        private async Task Publish(Func<Event, Task> action, Event @event)
        {
            bool successful = false;
            int attempts = 3;

            while (!successful && attempts-- > 0)
            {
                try
                {
                    await action(@event);
                    successful = true;
                }
                catch
                {
                    // capture for diagnostic reasons
                }
            }

            if (!successful)
            {
                // handle error
            }
        }

        /// <summary>
        /// Puts the specified identifier.
        /// </summary>
        /// <param name="id">The event identifer.</param>
        /// <param name="event">The updated event.</param>
        /// <returns>ActionResult.</returns>
        [HttpPut]
        [Route("{eid}")]
        public async Task<IHttpActionResult> UpdateEventAsync(string eid, ScheduleEvent @event)
        {
            long id;
            if (long.TryParse(eid, out id))
            {
                @event.id = id;
                var updated = await _eventWriter.UpdateAsync(Map(@event));
                var response = Ok(Map(updated));
                return response;
            }
            else
            {
                return new ResponseMessageResult(Request.CreateResponse(System.Net.HttpStatusCode.BadRequest, "The Event ID was not in the correct format."));
            }

        }
    }
}