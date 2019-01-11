using System;
using System.Configuration;
using System.Data.SqlClient;
using System.Threading.Tasks;
using Dapper;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IEventWriter"/> which utilizes dapper to interact with the backing store.
    /// </summary>
    /// <seealso cref="Veritema.Data.DapperAdapter" />
    /// <seealso cref="Veritema.Data.IEventWriter" />
    public class DapperEventWriter : DapperAdapter, IEventWriter
    {
        private const string ConnectionStringName = "v";
        private readonly ILocationReader _locationLoader;
        private readonly IConnectionStringResolver _resolver;


        /// <summary>
        /// Initializes a new instance of the <see cref="DapperEventWriter"/> class.
        /// </summary>
        /// <param name="resolver">The resolver.</param>
        /// <exception cref="System.ArgumentNullException"></exception>
        public DapperEventWriter(IConnectionStringResolver resolver, ILocationReader locationLoader)
        {
            if (resolver == null)
            {
                throw new ArgumentNullException(nameof(resolver));
            }
            if (locationLoader == null)
            {
                throw new ArgumentNullException(nameof(locationLoader));
            }
            _resolver = resolver;
            _locationLoader = locationLoader;
        }

        /// <summary>
        /// Creates the specified event.
        /// </summary>
        /// <param name="event">Details about the event to be created.</param>
        /// <returns>A representation of the serialized <see cref="Event"/>.</returns>
        public async Task<Event> CreateAsync(Event @event) => await UpsertKernelAsync(@event);

        /// <summary>
        /// Update an existing event.
        /// </summary>
        /// <param name="event">The event to be updated.</param>
        /// <returns>A representation of the serialzied <see cref="T:Veritema.Data.Event" />.</returns>
        public async Task<Event> UpdateAsync(Event @event) => await UpsertKernelAsync(@event);


        /// <summary>
        /// The core upsert implementation.
        /// </summary>
        /// <param name="event">The event to be upserted.</param>
        /// <returns>The updated event.</returns>
        /// <exception cref="Veritema.Data.ScheduleException">
        /// The event must start in the future.
        /// or
        /// The event must end after it starts.
        /// or
        /// The event must be at least 30 minutes in duration.
        /// </exception>
        private async Task<Event> UpsertKernelAsync(Event @event)
        {
            string connectionString = _resolver.Resolve(ConnectionStringName)
                                               .Match(
                                                    Some: v => v,
                                                    None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                                                    );

            if (@event.StartUtc < DateTime.UtcNow)
            {
                throw new ScheduleException("The event must start in the future.");
            }

            if (@event.EndUtc < @event.StartUtc)
            {
                throw new ScheduleException("The event must end after it starts.");
            }

            if (@event.EndUtc < @event.StartUtc.AddMinutes(30))
            {
                throw new ScheduleException("The event must be at least 30 minutes in duration.");
            }

            string sql = LoadScript("UpsertEvent.Sql");

            using (var connection = new SqlConnection(connectionString))
            {
                var record = new EventRecord
                {
                    Id = @event.Id > -1 ? @event.Id : 0,
                    Title = @event.Title,
                    Details = @event.Description,
                    End = new DateTimeOffset(@event.EndUtc),
                    Start = new DateTimeOffset(@event.StartUtc),
                    Confirmed = @event.Confirmed,
                    StyleId = @event.Style.HasValue ? (int)@event.Style.Value : new int?(),
                    LocationId = @event.Location != null ? @event.Location.Id : new int?(),
                    Updated = DateTimeOffset.Now,
                    TypeId = (char)@event.Type
                };
                record = await connection.QueryFirstAsync<EventRecord>(sql, record);
                @event = new Event
                {
                    Id = record.Id,
                    Title = record.Title,
                    Description = record.Details,
                    EndUtc = record.End.UtcDateTime,
                    StartUtc = record.Start.UtcDateTime,
                    Confirmed = record.Confirmed,
                    Style = record.StyleId.HasValue ? (MartialArtStyle)record.StyleId.Value : new MartialArtStyle?(),
                    Location = record.LocationId.HasValue ? await _locationLoader.GetAsync(record.LocationId.Value) : null,
                    Updated = record.Updated,
                     Type = (EventType)record.TypeId
                };
            }

            return @event;
        }
    }
}
