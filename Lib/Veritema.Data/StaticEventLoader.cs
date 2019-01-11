using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IEventLoader"/> which returns a fixed set of items.
    /// </summary>
    /// <seealso cref="Veritema.Data.IEventLoader" />
    public class StaticEventLoader : IEventLoader
    {
        private class GenerationContext
        {
            public int Id { get; set; }
        }

        /// <summary>
        /// Get all known events.
        /// </summary>
        /// <param name="from">The starting point from where events will be loaded.</param>
        /// <param name="to">The ending point to where events will be loaded.</param>
        /// <returns>Thet set of <see cref="Event" /> instances.</returns>
        public Task<IEnumerable<Event>> GetAsync(DateTime? from = default(DateTime?), DateTime? to = default(DateTime?))
        {
            var context = new GenerationContext();
            var events = new List<Event>();

            DateTime current =  new DateTime(DateTime.Now.Year, DateTime.Now.Month, 1);

            for (int i = 0; i < 3; i++)
            {
                events.AddRange(RenderMonth(current.Year, current.Month, context));
                current = current.AddMonths(1);
            }

            return Task.FromResult<IEnumerable<Event>>(events);
        }


        /// <summary>
        /// Creates the an event instance.
        /// </summary>
        /// <param name="id">The identifier.</param>
        /// <param name="message">The message.</param>
        /// <param name="location">The location.</param>
        /// <param name="start">The start.</param>
        /// <param name="minutes">The minutes.</param>
        /// <returns>Event.</returns>
        public static Event Create(int id, string message, string location, DateTime start, int minutes = 60)
        {
            var @event = new Event
            {
                Id = id,
                Description = message,
                StartUtc = start,
                EndUtc = start.AddMinutes(minutes),
                Location = new Location { Name = location }
            };
            return @event;
        }

        private IEnumerable<Event> EventsForDay(DateTime day, GenerationContext context)
        {
            switch (day.DayOfWeek)
            {
                case DayOfWeek.Friday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area",new DateTime(day.Ticks).AddMinutes(60 * 18));
                    break;
                case DayOfWeek.Monday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Hapkido", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Muay Thai","Ring", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Brazilian Jiujitsu", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 19 + 30));
                    break;
                case DayOfWeek.Saturday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area", new DateTime(day.Ticks).AddMinutes(60 * 10));
                    break;
                case DayOfWeek.Sunday:
                    break;
                case DayOfWeek.Thursday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Hapkido", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Muay Thai", "Ring", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Brazilian Jiujitsu", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 19 + 30));
                    break;
                case DayOfWeek.Tuesday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "No GI Jiujitsu", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Muay Thai", "Ring", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Brazilian Jiujitsu", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 19 + 30));
                    break;
                case DayOfWeek.Wednesday:
                    yield return Create(context.Id++, "Fitness Kickboxing", "Fitness Area", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Yudo", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Muay Thai", "Ring", new DateTime(day.Ticks).AddMinutes(60 * 18 + 30));
                    yield return Create(context.Id++, "Brazilian Jiujitsu", "Mats", new DateTime(day.Ticks).AddMinutes(60 * 19 + 30));
                    break;
            }
        }

        private IEnumerable<Event> RenderMonth(int year, int month, GenerationContext context)
        {
            DateTime fistDay = new DateTime(year, month, 1);
            int maxDays = DateTime.DaysInMonth(year, month);
            var events = new List<Event>();

            for (int i = 0; i < maxDays; i++)
            {
                DateTime currentDay = fistDay.AddDays(i);
                events.AddRange(EventsForDay(currentDay, context));
            }

            return events;
        }

        /// <summary>
        /// Retreive a specific event.
        /// </summary>
        /// <param name="id">event The identifier.</param>
        /// <returns>The <see cref="Event" /> identified by <paramref name="id" />.</returns>
        public Task<Event> GetAsync(long id)
        {
            return Task.FromResult(new Event {
                Description = "This was a persisted event",
                EndUtc = DateTime.Now.AddHours(1),
                Id =id,
                Location = new Location { Name = "RIMA" },
                StartUtc = DateTime.Now,
                Title = "Event"
            });
        }
    }
}
