using System;
using System.Collections.Generic;
using System.Configuration;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Mail;
using System.Reflection;
using System.Text;
using System.Threading.Tasks;
using System.Web.Configuration;
using Veritema.Data;
using Veritema.Notification.Configuration;

namespace Veritema.Notification
{
    /// <summary>
    /// An implementation of <see cref="INotifier"/> which utilizes SMTP
    /// </summary>
    /// <seealso cref="Veritema.Notification.INotifier" />
    public class SmtpNotifier : INotifier
    {
        private readonly Assembly Asm = Assembly.GetExecutingAssembly();

        /// <summary>
        /// Trigger that a event needs approved
        /// </summary>
        /// <param name="event">The event.</param>
        public Task ApproveEventAsync(Event @event)
        {
            var configuration = GetConfiguration();
            SmtpClient client = new SmtpClient(configuration.Server, configuration.Port)
            {
                Credentials = new NetworkCredential(configuration.User, configuration.Password),
                EnableSsl = configuration.EnableTls,
            };

            string template = LoadTemplate("PrivateLessonApproval");
            template = template.Replace("@Requestor", "Tedford Johnson");
            template = template.Replace("@ProfileLink", "https://veritema-dev.azurewebsites.net/profiles/123445");
            template = template.Replace("@Style", Humanize(@event.Style.Value));
            template = template.Replace("@Date", @event.StartUtc.Date.ToString("ddd MMM dd"));
            template = template.Replace("@Start", @event.StartUtc.ToString("HH:mm"));
            template = template.Replace("@Finish", @event.EndUtc.ToString("HH:mm"));
            template = template.Replace("@TZOffset", @event.StartUtc.ToString("zzz"));
            template = template.Replace("@accept", $"https://veritema-dev.azurewebsites.net/api/schedule/confirm/{@event.Id}");
            template = template.Replace("@move", $"https://veritema-dev.azurewebsites.net/schedule/{@event.Id}");

            MailMessage message = new MailMessage {
                From = new MailAddress("noreply@veritema.com")
            };
            message.To.Add(new MailAddress("lsu.tedford@gmail.com"));
            message.Subject = "Private Lesson Request";
            message.Body = template;
            message.BodyEncoding = Encoding.UTF8;
            message.IsBodyHtml = true;

            client.Send(message);


            return Task.FromResult(0);
        }

        /// <summary>
        /// Notify all parties that an event have been cancelled
        /// </summary>
        /// <param name="event">The event.</param>
        public void EventCancelled(Event @event)
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// Alert that an event has been created
        /// </summary>
        /// <param name="event">The event.</param>
        public void EventCreated(Event @event)
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// Notified intrested parties that an event is upcoming
        /// </summary>
        /// <param name="event">The event.</param>
        public void EventReminder(Event @event)
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// Notify interested parties that the details of an event has been updated
        /// </summary>
        /// <param name="event">The event.</param>
        public void EventUpdated(Event @event)
        {
            throw new NotImplementedException();
        }

        /// <summary>
        /// Gets the configuration for the SMTP client.
        /// </summary>
        /// <returns>The <see cref="SmtpConfigurationSection"/>.</returns>
        /// <exception cref="System.Configuration.ConfigurationErrorsException">Cannot locate the configuration information for the SMTP client</exception>
        private SmtpConfigurationSection GetConfiguration()
        {
            var section = WebConfigurationManager.GetSection("smtp") as SmtpConfigurationSection;
            if (section == null)
            {
                throw new ConfigurationErrorsException("Cannot locate the configuration information for the SMTP client");
            }
            return section;
        }

        /// <summary>
        /// Convert the <see cref="MartialArtStyle"/> instance into a human-friendly representation.
        /// </summary>
        /// <param name="style">The style to be translated.</param>
        /// <returns>The translated style</returns>
        private string Humanize(MartialArtStyle style)
        {
            switch (style)
            {
                case MartialArtStyle.Hapkido:
                    return "Hapkido";
                case MartialArtStyle.Judo:
                    return "Judo";
                case MartialArtStyle.JiuJitsu:
                    return "Brazillian Jiu-Jitsu";
                case MartialArtStyle.MuayThai:
                    return "Muay Thai";
                case MartialArtStyle.TaiChi:
                    return "Tai Chi";
                default:
                    return style.ToString();
            }
        }

        /// <summary>
        /// Loads the a script stored as an embedded resource.
        /// </summary>
        /// <param name="name">The name of the script.</param>
        /// <returns>the TSQL contained within the script.</returns>
        /// <exception cref="ResourceNotFoundException">Thrown when the specified script cannot be located</exception>
        protected string LoadTemplate(string name)
        {
            string path = Asm.GetManifestResourceNames().FirstOrDefault(i => i.IndexOf(name, StringComparison.OrdinalIgnoreCase) > -1);
            if (string.IsNullOrWhiteSpace(path))
            {
                throw new ResourceNotFoundException($"Unable to load the template {name}");
            }

            string template;
            using (var stream = Asm.GetManifestResourceStream(path))
            using (var reader = new StreamReader(stream))
            {
                template = reader.ReadToEnd();
            }
            return template;
        }
    }
}
