using System;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using System.Web.Http;
using LanguageExt;
using Newtonsoft.Json;
using Veritema.Data;
using System.Xml.Serialization;
using System.Xml;
using VeritemaMVC_3.Models;

namespace VeritemaMVC_3.Controllers
{
    /// <summary>
    /// Handle the management of Locations
    /// </summary>
    /// <seealso cref="System.Web.Http.ApiController" />
    [RoutePrefix("api/location")]
    public class LocationApiController : ApiController
    {
        private readonly ILocationReader _locationLoader;

        /// <summary>
        /// Initializes a new instance of the <see cref="LocationApiController"/> class.
        /// </summary>
        /// <param name="locationLoader">The location loader.</param>
        /// <exception cref="System.ArgumentNullException">locationLoader</exception>
        public LocationApiController(ILocationReader locationLoader)
        {
            if (locationLoader == null)
            {
                throw new ArgumentNullException(nameof(locationLoader));
            }

            _locationLoader = locationLoader;
        }

        /// <summary>
        /// Retrieve a location by Id.
        /// </summary>
        /// <param name="eid">The location identifier .</param>
        /// <returns>The server response.</returns>
        [HttpGet]
        [Route("{eid}")]
        public async Task<IHttpActionResult> Get(string eid)
        {
            IHttpActionResult result;
            long id;

            if (long.TryParse(eid, out id))
            {
                var location = await _locationLoader.GetAsync(id);
                result = Ok(location);
            }
            else
            {
                result = NotFound();
            }

            return result;
        }

        /// <summary>
        /// Get the known locations
        /// Modified 10.23.2016 (RG) - not sure why, but the XML response will populate the client-side lightbox - the json one will not
        /// </summary>
        /// <returns>The json representation of the location information</returns>
        [HttpGet]
        [Route("lookup")]
        public async Task<IHttpActionResult> GetLocationsLoookupAsync([FromUri]string id, [FromUri]string uid)
        {
            // todo cache
            var locations = await _locationLoader.GetAsync();

            var response = new ScheduleLocationComboResponse
            {
                Locations = locations.Map(i => new ScheduleLocationCombo { Id = i.Id.ToString(), Label = i.Name }).ToArray()
            };

            XmlSerializer serializer = new XmlSerializer(typeof(ScheduleLocationComboResponse) );
            StringBuilder sb = new StringBuilder();
            using (var writer = XmlTextWriter.Create(sb, new XmlWriterSettings { OmitXmlDeclaration= true }))
           {
               serializer.Serialize(writer, response);
                writer.Flush();
            }

            var httpResponse = new HttpResponseMessage {
                Content = new StringContent(sb.ToString(), Encoding.UTF8, "text/xml")
            };
            /*
            var httpResponse = new HttpResponseMessage
            {
                Content = new StringContent(JsonConvert.SerializeObject(locations.Select(i=>new { value= i.Id, text = i.Name }).ToArray()), Encoding.UTF8, "text/json")
            };
            */
            return ResponseMessage(httpResponse);
        }
    }
}