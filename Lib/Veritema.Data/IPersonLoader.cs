using System.Collections.Generic;
using System.Threading.Tasks;

namespace Veritema.Data
{

    /// <summary>
    /// Handles loading events from a backing store
    /// </summary>
    public interface IPersonLoader
    {

        /// <summary>
        /// Retrieve all of the people defined within the backing system
        /// </summary>
        /// <returns>The set of defined <see cref="Person"/> instances.</returns>
        Task<IEnumerable<Person>> GetAsync();

        /// <summary>
        /// Retreive a specific person by identifier.
        /// </summary>
        /// <param name="id">The identifier.</param>
        /// <returns>The <see cref="Event"/> identified by <paramref name="id"/>.</returns>
        Task<Person> GetAsync(int id);
    }
}
