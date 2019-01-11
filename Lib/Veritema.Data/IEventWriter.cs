using System.Threading.Tasks;

namespace Veritema.Data
{
    /// <summary>
    /// Handles persisting <see cref="Event"/> instance to the backing store
    /// </summary>
    public interface IEventWriter
    {
        /// <summary>
        /// Creates the specified event.
        /// </summary>
        /// <param name="event">Details about the event to be created.</param>
        /// <returns>A representation of the serialized <see cref="Event"/>.</returns>
        Task<Event> CreateAsync(Event @event);

        /// <summary>
        /// Update an existing event.
        /// </summary>
        /// <param name="event">The event to be updated.</param>
        /// <returns>A representation of the serialzied <see cref="Event"/>.</returns>
        Task<Event> UpdateAsync(Event @event);
    }
}
