using System;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Veritema.Data
{
    /// <summary>
    /// Handles loading events from a backing store
    /// </summary>
    public interface IEventLoader
    {
        /// <summary>
        /// Get all known events.
        /// </summary>
        /// <param name="from">The starting point from where events will be loaded.</param>
        /// <param name="to">The ending point to where events will be loaded.</param>
        /// <returns>Thet set of <see cref="Event" /> instances.</returns>
        Task<IEnumerable<Event>> GetAsync(DateTime? from= new DateTime?(), DateTime? to = new DateTime?());

        /// <summary>
        /// Retreive a specific event.
        /// </summary>
        /// <param name="id">event The identifier.</param>
        /// <returns>The <see cref="Event"/> identified by <paramref name="id"/>.</returns>
        Task<Event> GetAsync(long id);
    }
}
