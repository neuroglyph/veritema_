using System;
using System.Collections.Generic;
using System.Web.Mvc;
using Veritema.Data;
using Amazon.SimpleNotificationService;
using Amazon.SimpleNotificationService.Model;
using System.Text;
using System.IO;

namespace VeritemaMVC_3.Controllers
{
    public class FormData
    {
        public string Name { get; set; }
        public string Email { get; set; }
        public string Telephone { get; set; }
        public string Subject { get; set; }
        public string Message { get; set; }

        // at least make sure these are all initialized to empty string.
        public FormData()
        {
            Name = string.Empty;
            Email = string.Empty;
            Telephone = string.Empty;
            Subject = string.Empty;
            Message = string.Empty;
        }
        public override string ToString()
        {
            // Build Message to publish to Contact Us AWS Topic
            StringBuilder sb = new StringBuilder();
            sb.AppendLine(String.Format("Name: {0}", this.Name));
            sb.AppendLine(String.Format("Email: {0}", this.Email));
            sb.AppendLine(String.Format("Telephone: {0}", this.Telephone));
            sb.AppendLine(String.Format("Subject: {0}", this.Subject));
            sb.AppendLine(String.Format("Message: {0}", this.Message));
            return sb.ToString();
        }
    }

    public class ContactController : Controller
    {
        public ContactController()
        {
            var x = 1;
        }

        public ActionResult Index()
        {
            string text = string.Empty;
           // var image = Captcha.Generate(6, 100, 50, out text);
            return View();
        }

        [HttpGet]
        public JsonResult Submit(FormData data)
        {
            List<PublishResponse> publishResponses = new List<PublishResponse>();


            string accessKey = GetConfigValue("AWSAccessKeyId");
            string secretKey = GetConfigValue("AWSSecretKeyValue");
            var client = new AmazonSimpleNotificationServiceClient(accessKey, secretKey, Amazon.RegionEndpoint.USEast1);
            
            var request = new ListTopicsRequest();
            
            // only want the ContactUs Topic Arn
            // for now this is hard-coded - future refactor should place it in web config or database.
            // Looks like IAWSChannelLoader etc got wiped. For now, just run with web.config
            string contactUsARN = GetConfigValue("AWContactUsARN");

            /* Only one ARN, so following proof of concept not needed, commented out
            var listTopicsResponse = client.ListTopics(request);
            foreach (var topic in listTopicsResponse.Topics)
            {
              
            }
            */

            // Basic functionality works. Need to parse the publishResponse collection for specific service messages (failed, success, etc)
            // but for now simply assume if it published it was a success.
            publishResponses.Add(client.Publish(new PublishRequest
            {
                TopicArn = contactUsARN,
                Message = data.ToString()
            }));
            return Json(publishResponses, JsonRequestBehavior.AllowGet);
        }

        public ActionResult Directions()
        {
            return View();
        }

        [HttpGet]
        public System.Web.Mvc.FileResult GetVideo()
        {
            string filePath = Server.MapPath("/images/small_circle_mtn.mp4");

            //   using (FileStream stream = new FileStream(filePath, FileMode.Open))
            //       return File(stream, "video/mp4");
             
            var stream = new MemoryStream();
            using (StreamWriter writer = new StreamWriter(stream))
            {
                // using (CsvWriter csv = new CsvWriter(writer))
                using (FileStream fileStream = new FileStream(filePath, FileMode.Open))
                {
                    stream.Seek(0, SeekOrigin.Begin);
                    return File(stream, "video/mp4");
                }
            }
        }

        /// <summary>
        /// Returns config value, if exists and found, or empty string.
        /// </summary>
        /// <param name="key">web config key</param>
        /// <returns>config value as string</returns>
        private string GetConfigValue(string key)
        {
            return !String.IsNullOrEmpty(System.Configuration.ConfigurationManager.AppSettings[key]) ? System.Configuration.ConfigurationManager.AppSettings[key].ToString() : string.Empty;

        }

    }
}