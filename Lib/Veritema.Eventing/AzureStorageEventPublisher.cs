using System;
using System.Threading.Tasks;
using Microsoft.Azure;
using Microsoft.WindowsAzure.Storage;
using Microsoft.WindowsAzure.Storage.Queue;
using Newtonsoft.Json;
using Veritema.Data;

namespace Veritema.Eventing
{
    /// <summary>
    /// An implementation of <see cref="IEventPublisher"/> which utilizes azure queues
    /// </summary>
    /// <seealso cref="Veritema.Eventing.IEventPublisher" />
    public class AzureStorageEventPublisher : IEventPublisher
    {
        /// <summary>
        /// Fires then an event was created in the system
        /// </summary>
        /// <param name="event">The event.</param>
        public async Task OnCreatedAsync(Event @event)
        {
            var account = GetAccount();
            var client = account.CreateCloudQueueClient();

            var queue = client.GetQueueReference(QueueNames.EventCreated);
            await queue.CreateIfNotExistsAsync();

            var e = new EventCreated
            {
                Created = DateTimeOffset.Now,
                EventId = @event.Id
            };

            var message = new CloudQueueMessage(JsonConvert.SerializeObject(e));
            await queue.AddMessageAsync(message);
        }


        /// <summary>
        /// Gets the account.
        /// </summary>
        /// <returns>CloudStorageAccount.</returns>
        private CloudStorageAccount GetAccount()=> CloudStorageAccount.Parse(CloudConfigurationManager.GetSetting("Storage"));



    }
}
