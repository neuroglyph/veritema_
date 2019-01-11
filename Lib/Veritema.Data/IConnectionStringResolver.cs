using LanguageExt;

namespace Veritema.Data
{
    /// <summary>
    /// Provides the connection string used to connect to a backing store
    /// </summary>
    public interface IConnectionStringResolver
    {
        /// <summary>
        /// Resolves the connection string.
        /// </summary>
        /// <param name="name">The name of the connection string.</param>
        /// <returns>The connection string or an em empty monad.</returns>
        Option<string> Resolve(string name);
    }
}
