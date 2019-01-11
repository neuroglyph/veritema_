using System;
using System.Threading.Tasks;

namespace Veritema.Data
{
    /// <summary>
    /// Removes the event from the backing store
    /// </summary>
    public interface IEventScrubber
    {
        /// <summary>
        /// Deletes the event asynchronously.
        /// </summary>
        /// <param name="id">The event identifier.</param>
        Task DeleteAsync(Int64 id);
    }
}
