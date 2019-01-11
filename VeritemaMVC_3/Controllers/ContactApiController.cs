using Amazon.SimpleNotificationService;
using Amazon.SimpleNotificationService.Model;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http; 
using System.Text;
using System.Threading.Tasks;
using LanguageExt;
using Newtonsoft.Json;
using Veritema.Data;
using System.Xml.Serialization;
using System.Xml;
using VeritemaMVC_3.Models;



namespace VeritemaMVC_3.Controllers
{
  
    /// <summary>
    /// Handle the management of Contact Us Form
    /// </summary>
    /// <seealso cref="System.Web.Http.ApiController" />
    [RoutePrefix("api/contact")]
    public class ContactApiController : ApiController
    {
        private readonly ILocationReader _locationLoader;

        /// <summary>
        /// Initializes a new instance of the <see cref="LocationApiController"/> class.
        /// </summary>
        /// <param name="locationLoader">The location loader.</param>
        /// <exception cref="System.ArgumentNullException">locationLoader</exception>
        public ContactApiController(ILocationReader locationLoader)
        {
            if (locationLoader == null)
            {
                throw new ArgumentNullException(nameof(locationLoader));
            }

            _locationLoader = locationLoader;
        }
  
        /// <summary>
        /// Proof of concept - we can do much better than this, implementation of async, store credentials much
        /// more securely. However, just interested to see if I can get this up and running for now. RG
        /// </summary>
        /// <returns></returns>
        [Route("contact/submit")]
        // public List<PublishResponse> Submit(FormData data)
        public async Task<IHttpActionResult> Submit(FormatException data)
        {
            IHttpActionResult result = null; 

            string accessKey = String.IsNullOrEmpty(System.Configuration.ConfigurationManager.AppSettings["AWSAccessKey"].ToString()) ? string.Empty : System.Configuration.ConfigurationManager.AppSettings["AWSAccessKey"].ToString();
            string secretKey = String.IsNullOrEmpty(System.Configuration.ConfigurationManager.AppSettings["AWSSecretKey"].ToString()) ? string.Empty : System.Configuration.ConfigurationManager.AppSettings["AWSSecretKey"].ToString();
            var client = new AmazonSimpleNotificationServiceClient(accessKey, secretKey, Amazon.RegionEndpoint.USEast1);


            var request = new ListTopicsRequest();

            var listTopicsResponse = client.ListTopics(request);

            List<PublishResponse> publishResponses = new List<PublishResponse>();

            foreach (var topic in listTopicsResponse.Topics)
            {
                publishResponses.Add(client.Publish(new PublishRequest
                {
                    TopicArn = topic.TopicArn,
                    Message = "This is a test from the ContactUs Web MVC Controller"
                })
                );
            }
            return result;
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

            /*
            if (long.TryParse(eid, out id))
            {
                var location = await _locationLoader.GetAsync(id);
                result = Ok(location);
            }
            else
            {
                result = NotFound();
            }
            */

            return null;
        }

    }
}
