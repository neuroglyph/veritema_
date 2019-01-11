using System;
using System.Collections.Generic;
using System.Diagnostics;

namespace Veritema.Data
{
    /// <summary>
    /// Describes a physical location
    /// </summary>
    [DebuggerDisplay("{DebuggerDisplay}")]
    public class Location
    {
        private string DebuggerDisplay => $"[{Id}]{Name}";

        /// <summary>
        /// Gets or sets the location identifier.
        /// </summary>
        /// <value>The identifier.</value>
        public int Id { get; set; }

        /// <summary>
        /// Gets or sets the city.
        /// </summary>
        /// <value>The city.</value>
        public string City { get; set; }

        /// <summary>
        /// Gets or sets the methods by which this location an be contacted.
        /// </summary>
        /// <value>The contacts.</value>
        public IEnumerable<Uri> Contacts { get; set; } = new Uri[0];

        /// <summary>
        /// Gets or sets the name of the location.
        /// </summary>
        /// <value>The name.</value>
        public string Name { get; set; }

        /// <summary>
        /// Gets or sets the state.
        /// </summary>
        /// <value>The state.</value>
        public string State { get; set; }

        /// <summary>
        /// Gets or sets the street address line 1.
        /// </summary>
        /// <value>The street.</value>
        public string Street { get; set; }

        /// <summary>
        /// Gets or sets the street address line 2.
        /// </summary>
        /// <value>The street2.</value>
        public string Street2 { get; set; }

        /// <summary>
        /// Gets or sets the zip code for the location.
        /// </summary>
        /// <value>The zip.</value>
        public string Zip { get; set; }


    }
}
