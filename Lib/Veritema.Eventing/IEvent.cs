using System;

namespace Veritema.Eventing
{
    /// <summary>
    /// Represents a notable action which occured within the system
    /// </summary>
    public interface IEvent
    {
        /// <summary>
        /// Gets the event epoch.
        /// </summary>
        /// <value>The created.</value>
        DateTimeOffset Created { get;  }
    }
}
