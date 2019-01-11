using System;
using System.Collections.Generic;
using LanguageExt;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IConnectionStringResolver"/> which utilizes a fixed data set
    /// </summary>
    /// <seealso cref="Veritema.Data.IConnectionStringResolver" />
    public class StaticConnectionStringResolver : IConnectionStringResolver
    {
        private readonly Dictionary<string, string> _connectionStrings;

        /// <summary>
        /// Initializes a new instance of the <see cref="StaticConnectionStringResolver"/> class.
        /// </summary>
        /// <param name="connectionStrings">The connection strings.</param>
        /// <exception cref="System.ArgumentNullException"></exception>
        public StaticConnectionStringResolver(IDictionary<string,string> connectionStrings)
        {
            if (connectionStrings == null)
            {
                throw new ArgumentNullException(nameof(connectionStrings));
            }

            _connectionStrings = new Dictionary<string, string>(connectionStrings, StringComparer.OrdinalIgnoreCase);
        }

        /// <summary>
        /// Resolves the connection string.
        /// </summary>
        /// <param name="name">The name of the connection string.</param>
        /// <returns>The connection string.</returns>
        public Option<string> Resolve(string name)
        {
            string connectionString = null;

            _connectionStrings.TryGetValue(name, out connectionString);

            return string.IsNullOrWhiteSpace(connectionString) ? Option<string>.None : Option<string>.Some(connectionString);
        }
    }
}
