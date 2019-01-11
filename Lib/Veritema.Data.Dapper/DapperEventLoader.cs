using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.Linq;
using System.Threading.Tasks;
using Dapper;
using LanguageExt;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IEventLoader"/> which utilizes Dapper to interact with a SQL backing store
    /// </summary>
    /// <seealso cref="Veritema.Data.IEventLoader" />
    public class DapperEventLoader : DapperAdapter, IEventLoader
    {
        private const string ConnectionStringName = "v";
        private readonly IConnectionStringResolver _resolver;
        private readonly ILocationReader _locationReader;

        /// <summary>
        /// Initializes a new instance of the <see cref="DapperEventLoader" /> class.
        /// </summary>
        /// <param name="resolver">Resolver the backend store.</param>
        /// <param name="locationReader">Hydrates location information from the backing store.</param>
        /// <exception cref="System.ArgumentNullException">resolver</exception>
        public DapperEventLoader(IConnectionStringResolver resolver, ILocationReader locationReader)
        {
            if (resolver == null)
            {
                throw new ArgumentNullException(nameof(resolver));
            }

            if (locationReader == null)
            {
                throw new ArgumentNullException(nameof(locationReader));
            }
            _resolver = resolver;
            _locationReader = locationReader;
        }

        /// <summary>
        /// Gets the set of defined events.
        /// </summary>
        /// <param name="from">The starting point from where events will be loaded.</param>
        /// <param name="to">The ending point to where events will be loaded.</param>
        /// <returns>The existing <see cref="Event" /> instances contained within the backing store.</returns>
        public async Task<IEnumerable<Event>> GetAsync(DateTime? from = default(DateTime?), DateTime? to = default(DateTime?))
        {
            var events = await Query(LoadScript("QueryEvents.sql"), new { from = from, to = to });
            return events;
        }

        /// <summary>
        /// Retreive a specific event.
        /// </summary>
        /// <param name="id">event The identifier.</param>
        /// <returns>The <see cref="T:Veritema.Data.Event" /> identified by <paramref name="id" />.</returns>
        /// <exception cref="System.NotImplementedException"></exception>
        public async Task<Event> GetAsync(long id)
        {
            var events = await Query(LoadScript("QueryEventById.sql"), new {id = id });
            return events.FirstOrDefault();
        }

        /// <summary>
        /// Issue the specified TSQL query against the backing store.
        /// </summary>
        /// <param name="tsql">The TSQL to be executed.</param>
        /// <param name="parameters">The request parameters.</param>
        /// <returns>The meterialized events.</returns>
        private async Task<IEnumerable<Event>> Query(string tsql, object parameters)
        {

            string connectionString = _resolver.Resolve(ConnectionStringName)
                          .Match(
                               Some: v => v,
                               None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                          );

            IEnumerable<Event> events;
            using (var connection = new SqlConnection(connectionString))
            {
                var records = await connection.QueryAsync<EventRecord>(tsql, parameters);
                events= records.Select((async (i) => await MapAsync(i))).Select(i=>i.Result).ToArray();
            }
            return events;
        }


        /// <summary>
        /// Convert the database representation to the abstraction.
        /// </summary>
        /// <param name="record">The database record.</param>
        /// <param name="location">The location record.</param>
        /// <returns>The <see cref="Event"/> representation.</returns>
        private async Task<Event> MapAsync(EventRecord record)
        {
            var @event = new Event
            {
                Id = record.Id,
                Description = record.Details,
                Title = record.Title,
                StartUtc = record.Start.UtcDateTime,
                EndUtc = record.End.UtcDateTime,
                Confirmed = record.Confirmed,
                Updated = record.Updated,
                Type = (EventType)record.TypeId,
                Style = record.StyleId.HasValue ? (MartialArtStyle)record.StyleId.Value : new MartialArtStyle?()
            };

            if (record.LocationId.HasValue)
            {
                @event.Location = await _locationReader.GetAsync(record.LocationId.Value);
            }

            return @event;
        }
    }
}
