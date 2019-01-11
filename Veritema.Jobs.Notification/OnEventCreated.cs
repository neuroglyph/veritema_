using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Microsoft.Azure.WebJobs;
using Newtonsoft.Json;
using Veritema.Data;
using Veritema.Eventing;
using Veritema.Notification;

namespace Veritema.Jobs.Notification
{
    /// <summary>
    /// A service which processes <see cref="EventCreated"/> events.
    /// </summary>
    public class OnEventCreated
    {
        private readonly IEventLoader _loader;
        private readonly INotifier _notifier;

        /// <summary>
        /// Initializes a new instance of the <see cref="OnEventCreated"/> class.
        /// </summary>
        /// <param name="loader">The loader.</param>
        /// <param name="notifier">The notifier.</param>
        /// <exception cref="System.ArgumentNullException">
        /// loader
        /// or
        /// notifier
        /// </exception>
        public OnEventCreated(IEventLoader loader, INotifier notifier)
        {
            if (loader == null)
            {
                throw new ArgumentNullException(nameof(loader));
            }
            if (notifier == null)
            {
                throw new ArgumentNullException(nameof(notifier));
            }
            _loader = loader;
            _notifier = notifier;
        }

        // This function will get triggered/executed when a new message is written 
        // on an Azure Queue called queue.

        /// <summary>
        /// process queue message as an asynchronous operation.
        /// </summary>
        /// <param name="message">The message.</param>
        /// <param name="log">The log.</param>
        /// <returns>Task.</returns>
        public async Task ProcessQueueMessageAsync([QueueTrigger(QueueNames.EventCreated)] string message, TextWriter log)
        {
            Console.WriteLine($"Processing {message}");

            try
            {
                var @event = JsonConvert.DeserializeObject<EventCreated>(message);
                var details = await _loader.GetAsync(@event.EventId);

                // schedule reminder

                if (details.Type == EventType.Class)
                {
                }
                else if (details.Type == EventType.PrivateLesson)
                {
                    await _notifier.ApproveEventAsync(details);
                }
                else if (details.Type == EventType.Seminar)
                {

                }
                else
                {

                }
                
                log.WriteLine(message);
            }
            catch 
            {
                // log the error  
                throw;
            }
        }
    }
}
