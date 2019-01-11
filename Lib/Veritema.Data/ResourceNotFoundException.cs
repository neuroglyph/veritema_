using System;

namespace Veritema.Data
{
    /// <summary>
    /// Describes the inability to access a dependency
    /// </summary>
    /// <seealso cref="System.Exception" />
    public class ResourceNotFoundException : Exception
    {
        /// <summary>
        /// Initializes a new instance of the <see cref="ResourceNotFoundException"/> class.
        /// </summary>
        /// <param name="message">The message that describes the error.</param>
        public ResourceNotFoundException(string message) : base(message)
        {
        }
    }
}
