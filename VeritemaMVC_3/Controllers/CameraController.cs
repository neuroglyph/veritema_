using ASPNET_Web_Api_SelfHost_VideoStreaming.Streams;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Threading.Tasks;
using System.Web.Http;

namespace ASPNET_Web_Api_SelfHost_VideoStreaming.Controllers
{
    public class CameraController : ApiController
    {
        [HttpGet]
        public HttpResponseMessage FromImages()
        {
            var imageStream = new ImageStream();
            Func<Stream, HttpContent, TransportContext, Task> func = imageStream.WriteToStream;
            var response = Request.CreateResponse();
            response.Content = new PushStreamContent(func);
            response.Content.Headers.Remove("Content-Type");
            response.Content.Headers.TryAddWithoutValidation("Content-Type", "multipart/x-mixed-replace;boundary=" + imageStream.Boundary);
            return response;
        }

        [HttpGet]
        public HttpResponseMessage FromVideo(string videoName)
        {
            var video = new VideoStream(videoName);
            Func<Stream, HttpContent, TransportContext, Task> func = video.WriteToStream;
            var response = Request.CreateResponse();
            response.Content = new PushStreamContent(func, new MediaTypeHeaderValue("video/mp4"));

            return response;
        }
    }
}