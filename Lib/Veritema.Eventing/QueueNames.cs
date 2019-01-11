namespace Veritema.Eventing
{
    /// <summary>
    /// Defines the name of queues within the system
    /// </summary>
    public static class QueueNames
    {
        /// <summary>
        /// The queue which receives <see cref="EventCreated"/> messages
        /// </summary>
        public const string EventCreated = "veritema-event-created";
    }
}
