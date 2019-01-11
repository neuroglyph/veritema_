using System;
using System.Net;
using System.Threading.Tasks;
using System.Web;
using System.Web.Mvc;
using System.Web.Routing;
using Veritema.Data;

namespace VeritemaMVC_3.Controllers
{
    [RoutePrefix("schedule")]
    public class ScheduleController : Controller
    {
        private readonly IEventLoader _eventLoader;
        private readonly IEventWriter _eventWriter;

        /// <summary>
        /// Initializes a new instance of the <see cref="ScheduleController" /> class.
        /// </summary>
        /// <exception cref="System.ArgumentNullException">eventLoader
        /// or
        /// eventWriter</exception>
        public ScheduleController(IEventLoader eventLoader, IEventWriter eventWriter)
        { 
            if (eventLoader == null)
            {
                throw new ArgumentNullException(nameof(eventLoader));
            }

            if (eventWriter == null)
                throw new ArgumentNullException(nameof(eventWriter));

            _eventLoader = eventLoader;
        }

        [HttpGet]
        public ActionResult Index() => View();

        [HttpGet]
        [Route("Details/{eid}")]
        public async Task<ActionResult> Details(string eid)
        {
            long id;
            if (long.TryParse(eid, out id))
            {
                var @event = await _eventLoader.GetAsync(id);
                ViewBag.Name = @event.Title;
                ViewBag.Description = @event.Description;
                ViewBag.Style = "Hapkido";
                ViewBag.Location = @event.Location;
                ViewBag.Instructor = "Master T";

                if (Request.IsAjaxRequest())
                    return PartialView();
                else
                    return View();
            }
            else
            {
                Response.StatusCode = (int)HttpStatusCode.NotFound;
                return null;
            }
        }
    }

}