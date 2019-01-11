using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.Linq;
using System.Threading.Tasks;
using Dapper;
using LanguageExt;
using Newtonsoft.Json;

namespace Veritema.Data
{
    /// <summary>
    /// An implemenation of <see cref="IPersonLoader"/> which handles hydrating <see cref="Person"/> instances
    /// from a SQL store using Dapper
    /// </summary>
    /// <seealso cref="Veritema.Data.DapperAdapter" />
    /// <seealso cref="Veritema.Data.IPersonLoader" />
    public class DapperPersonLoader : DapperAdapter, IPersonLoader
    {
        private const string ConnectionStringName = "v";
        private readonly IConnectionStringResolver _resolver;

        /// <summary>
        /// Initializes a new instance of the <see cref="DapperPersonLoader"/> class.
        /// </summary>
        /// <param name="resolver">The resolver.</param>
        /// <exception cref="System.ArgumentNullException">resolver</exception>
        public DapperPersonLoader(IConnectionStringResolver resolver)
        {
            if (resolver == null)
            {
                throw new ArgumentNullException(nameof(resolver));
            }
            _resolver = resolver;
        }

        /// <summary>
        /// Retreive a specific event.
        /// </summary>
        /// <param name="id">event The identifier.</param>
        /// <returns>The <see cref="T:Veritema.Data.Event" /> identified by <paramref name="id" />.</returns>
        public async Task<Person> GetAsync(int id)
        {
            var people = await QueryAsync(LoadScript("QueryPersonById.sql"), new { id = id });
            return people.FirstOrDefault();
        }


        /// <summary>
        /// get as an asynchronous operation.
        /// </summary>
        /// <returns>The set of defined <see cref="T:Veritema.Data.Person" /> instances.</returns>
        public async Task<IEnumerable<Person>> GetAsync() => await QueryAsync(LoadScript("QueryPeople.sql"), new { });

        /// <summary>
        /// Issue the specified TSQL query against the backing store.
        /// </summary>
        /// <param name="tsql">The TSQL to be executed.</param>
        /// <param name="parameters">The request parameters.</param>
        /// <returns>The meterialized events.</returns>
        private async Task<IEnumerable<Person>> QueryAsync(string tsql, object parameters)
        {

            string connectionString = _resolver.Resolve(ConnectionStringName)
                          .Match(
                               Some: v => v,
                               None: () => { throw new ConfigurationErrorsException($"Cannot load the configuration string with name {{{ConnectionStringName}}}"); }
                          );

            IEnumerable<Person> people;
            using (var connection = new SqlConnection(connectionString))
            {
                var records = await connection.QueryAsync(tsql, parameters);
                people = records.Map(i => ToPerson(i)).Cast<Person>().ToArray();

            }
            return people;
        }


        /// <summary>
        /// Convert the database representation to the abstraction.
        /// </summary>
        /// <param name="record">The person record.</param>
        /// <returns>The <see cref="Person"/> representation.</returns>
        private static Person ToPerson(dynamic record)
        {
            Person person = JsonConvert.DeserializeObject<Person>(record.Value);
            person.Id = record.Id;
            person.Updated = record.Updated;
            return person;
        }
    }
}
