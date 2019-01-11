using System.Threading.Tasks;
using Veritema.Data;

namespace Veritema.Eventing
{
    /// <summary>
    /// Orchestrates the publishing of an event to the queuing system
    /// </summary>
    public interface IEventPublisher
    {
        /// <summary>
        /// Fires then an event was created in the system
        /// </summary>
        /// <param name="event">The event.</param>
        Task OnCreatedAsync(Event @event);
    }
}
