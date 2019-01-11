using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using LanguageExt;

namespace Veritema.Data
{
    /// <summary>
    /// An implementation of <see cref="IConnectionStringResolver"/> which utilizes 
    /// </summary>
    /// <seealso cref="Veritema.Data.IConnectionStringResolver" />
    public class DynamicConnectionStringResolver : IConnectionStringResolver
    {
        private readonly Func<string, string> _resolver;

        /// <summary>
        /// Initializes a new instance of the <see cref="DynamicConnectionStringResolver"/> class.
        /// </summary>
        /// <param name="resolver">The resolver.</param>
        /// <exception cref="System.ArgumentNullException">resolver</exception>
        public DynamicConnectionStringResolver(Func<string, string> resolver)
        {
            if (resolver == null)
            {
                throw new ArgumentNullException(nameof(resolver));
            }

            _resolver = resolver;
        }

        /// <summary>
        /// Resolves the connection string.
        /// </summary>
        /// <param name="name">The name of the connection string.</param>
        /// <returns>The connection string or an em empty monad.</returns>
        /// <exception cref="System.NotImplementedException"></exception>
        public Option<string> Resolve(string name)
        {
            throw new NotImplementedException();
        }
    }
}
