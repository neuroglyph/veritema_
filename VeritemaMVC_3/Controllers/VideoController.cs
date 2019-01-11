using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using System.Web.Http;

namespace ASPNET_Web_Api_SelfHost_VideoStreaming.Controllers
{
    public class VideosController : ApiController
    {
        public HttpResponseMessage Get()
        {
            IEnumerable<string> videos = new List<string>() { "Christmas", "Illumination" };
            HttpResponseMessage response = Request.CreateResponse(HttpStatusCode.OK, videos);
            return response;
        }
    }
}