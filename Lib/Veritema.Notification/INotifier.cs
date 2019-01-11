using System.Threading.Tasks;
using Veritema.Data;

namespace Veritema.Notification
{
    /// <summary>
    /// Represents an entity which is capable of sending notifications
    /// </summary>
    public interface INotifier
    {
        /// <summary>
        /// Trigger that a event needs approved
        /// </summary>
        /// <param name="event">The event.</param>
        Task ApproveEventAsync(Event @event);

        /// <summary>
        /// Alert that an event has been created
        /// </summary>
        /// <param name="event">The event.</param>
        void EventCreated(Event @event);

        /// <summary>
        /// Notify all parties that an event have been cancelled
        /// </summary>
        /// <param name="event">The event.</param>
        void EventCancelled(Event @event);

        /// <summary>
        /// Notify interested parties that the details of an event has been updated
        /// </summary>
        /// <param name="event">The event.</param>
        void EventUpdated(Event @event);

        /// <summary>
        /// Notified intrested parties that an event is upcoming
        /// </summary>
        /// <param name="event">The event.</param>
        void EventReminder(Event @event);
    }
}
