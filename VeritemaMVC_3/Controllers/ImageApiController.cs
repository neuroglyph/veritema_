using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using System.Web.Mvc;
using System.IO;

namespace VeritemaMVC_3.Controllers
{
    [System.Web.Http.RoutePrefix("api/image")]
    public class ImageApiController : ApiController
    {
        [System.Web.Http.Route("image/getvideo")]
        public FileStreamResult GetVideo()
        {

            //string filePath = Server.MapPath("/images/small_circle_mtn.mp4");

            using (FileStream stream = new FileStream("~/images/small_circle_mtn.mp4", FileMode.Open))
                return new FileStreamResult(stream, "video/mp4");
        }
    }
}
