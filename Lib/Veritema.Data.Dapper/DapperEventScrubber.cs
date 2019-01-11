using System;
using System.Configuration;
using System.Data.SqlClient;
using System.Threading.Tasks;
using Dapper;

namespace Veritema.Data
{
    /// <summary>
    /// Orchestrates the removal of an <see cref="Event"/> from the backing store.
    /// </summary>
    /// <seealso cref="Veritema.Data.DapperAdapter" />
    /// <seealso cref="Veritema.Data.IEventScrubber" />
    public class DapperEventScrubber : DapperAdapter, IEventScrubber
    {
        private const string ConnectionStringName = "v";
        private readonly IConnectionStringResolver _resolver;

        /// <summary>
        /// Initializes a new instance of the <see cref="DapperEventScrubber"/> class.
        /// </summary>
        /// <param name="connectionStringResolver">The connection string resolver.</param>
        /// <exception cref="System.ArgumentNullException">connectionStringResolver</exception>
        public DapperEventScrubber(IConnectionStringResolver connectionStringResolver)
        {
            if (connectionStringResolver == null)
            {
                throw new ArgumentNullException(nameof(connectionStringResolver));
            }
            _resolver = connectionStringResolver;
        }

        /// <summary>
        /// Deletes the event asynchronously.
        /// </summary>
        /// <param name="id">The event identifier.</param>
        public async Task DeleteAsync(long id)
        {
            string connectionString = _resolver.Resolve(ConnectionStringName)
                                   .Match(
                                        Some: v => v,
                                        None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                                   );

            using (var connection = new SqlConnection(connectionString))
            {
                await connection.ExecuteAsync("DELETE from [v].[Event] where Id = @id", new { id = id });
            }
        }
    }
}
