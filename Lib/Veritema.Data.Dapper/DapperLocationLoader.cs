using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.Linq;
using System.Threading.Tasks;
using Dapper;
using Newtonsoft.Json;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="ILocationReader"/> which utilizes Dapper to interact with a backing SQL store.
    /// </summary>
    /// <seealso cref="Veritema.Data.DapperAdapter" />
    /// <seealso cref="Veritema.Data.ILocationReader" />
    public class DapperLocationLoader : DapperAdapter, ILocationReader
    {
        private const string ConnectionStringName = "v";
        private readonly IConnectionStringResolver _resolver;

        /// <summary>
        /// Initializes a new instance of the <see cref="DapperLocationLoader"/> class.
        /// </summary>
        /// <param name="resolver">The resolver.</param>
        /// <exception cref="System.ArgumentNullException">resolver</exception>
        public DapperLocationLoader(IConnectionStringResolver resolver)
        {
            if (resolver == null)
            {
                throw new ArgumentNullException(nameof(resolver));
            }
            _resolver = resolver;
        }


        /// <summary>
        /// Gets the defined locations.
        /// </summary>
        /// <returns>The set of locations.</returns>
        public async Task<IEnumerable<Location>> GetAsync()
        {
            string connectionString = _resolver.Resolve(ConnectionStringName)
                                   .Match(
                                        Some: v => v,
                                        None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                                   );


            string sql = LoadScript("QueryLocations.sql");

            IEnumerable<Location> locations;
            using (var connection = new SqlConnection(connectionString))
            {
                var records = await connection.QueryAsync(sql);
                locations = records.Select(i => Map(i)).Cast<Location>().ToArray();
            }

            return locations;
        }

        /// <summary>
        /// get a single location from the backing store.
        /// </summary>
        /// <param name="id">The location identifier.</param>
        /// <returns>A representation of the location.</returns>
        public async Task<Location> GetAsync(long id)
        {
            string connectionString = _resolver.Resolve(ConnectionStringName)
                       .Match(
                            Some: v => v,
                            None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                       );


            string sql = LoadScript("QueryLocationById.sql");

            Location location;
            using (var connection = new SqlConnection(connectionString))
            {
                var record = await connection.QuerySingleOrDefaultAsync<dynamic>(sql,new { id = id});
                location = record == null ? null : Map(record);
            }

            return location;
        }


        /// <summary>
        /// Maps the hydrated record into a <see cref="Location"/> instance.
        /// </summary>
        /// <param name="record">The database record.</param>
        /// <returns>The initialized <see cref="Location"/>.</returns>
        private Location Map(dynamic record) => 
            new Location {
                Id = record.Id,
                Name = record.Name,
                Contacts = JsonConvert.DeserializeObject<string[]>((string)record.Contacts ?? string.Empty).Select(i=>new Uri(i)).ToArray(),
                Street = record.Street,
                Street2 = record.Street2,
                City = record.City,
                State = record.State,
                Zip = record.Zip
            };
    }
}
