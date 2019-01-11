using System.Collections.Generic;
using System.Threading.Tasks;

namespace Veritema.Data
{
    /// <summary>
    /// Handles loading location information from a backing store
    /// </summary>
    public interface ILocationReader
    {
        /// <summary>
        /// Gets the defined locations.
        /// </summary>
        /// <returns>The set of locations.</returns>
        Task<IEnumerable<Location>> GetAsync();

        /// <summary>
        /// Gets the defined locations.
        /// </summary>
        /// <param name="id">The location identifier.</param>
        /// <returns>A representation of the location.</returns>
        Task<Location> GetAsync(long id);
    }
}
