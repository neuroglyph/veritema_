using System;

namespace Veritema.Eventing
{
    /// <summary>
    /// Represnts the that an event was created
    /// </summary>
    public class EventCreated : IEvent
    {
        /// <summary>
        /// Gets or sets the time when the event was created.
        /// </summary>
        /// <value>The created.</value>
        public DateTimeOffset Created { get; set; }

        /// <summary>
        /// Gets or sets the identifier of the created event.
        /// </summary>
        /// <value>The event identifier.</value>
        public long EventId { get; set; }
    }
}
