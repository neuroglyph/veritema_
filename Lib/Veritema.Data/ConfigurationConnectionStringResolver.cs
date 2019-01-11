using System.Configuration;
using LanguageExt;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IConnectionStringResolver"/> which source the process configuration file
    /// </summary>
    /// <seealso cref="Veritema.Data.IConnectionStringResolver" />
    public class ConfigurationConnectionStringResolver : IConnectionStringResolver
    {
        /// <summary>
        /// Resolves the connection string.
        /// </summary>
        /// <param name="name">The name of the connection string.</param>
        /// <returns>The connection string or an em empty monad.</returns>
        public Option<string> Resolve(string name) => ConfigurationManager.ConnectionStrings[name] != null ? Option<string>.Some(ConfigurationManager.ConnectionStrings[name].ConnectionString) : Option<string>.None;
    }
}
