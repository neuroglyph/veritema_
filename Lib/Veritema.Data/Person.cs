using System;
using System.Collections.Generic;

namespace Veritema.Data
{
    /// <summary>
    /// Represents the concept of a person (user, instructor, etc)
    /// </summary>
    public class Person
    {


        /// <summary>
        /// Gets or sets various contact methods for the person.
        /// </summary>
        /// <value>The contacts.</value>
        public IEnumerable<Uri> Contacts { get; set; } = new Uri[0];

        /// <summary>
        /// Gets or sets the location identifier.
        /// </summary>
        /// <value>The identifier.</value>
        public int Id { get; set; }

        /// <summary>
        /// Gets or sets the first name
        /// </summary>
        /// <value>The first name.</value>
        public string First { get; set; }

        /// <summary>
        /// Gets or sets the last name
        /// </summary>
        /// <value>The last name.</value>
        public string Last { get; set; }

        /// <summary>
        /// Gets or sets the description.
        /// </summary>
        /// <value>The description.</value>
        public string Description {get; set;}

        /// <summary>
        /// Gets or sets the image url.
        /// </summary>
        /// <value>The image url.</value>
        public Uri ImageUrl { get;   set;}

        /// <summary>
        /// Indicates whether this is a temporary account.
        /// </summary>
        /// <value>The temporary property.</value>
        public bool Temporary { get; set; }

        /// <summary>
        /// Gets or sets the timestamp of when the record was last updated.
        /// </summary>
        /// <value>The updated.</value>
        public DateTimeOffset Updated { get; set; }

        /// <summary>
        /// Gets or sets a value indicating whether this <see cref="Person"/> is verified.
        /// </summary>
        /// <value><c>true</c> if verified; otherwise, <c>false</c>.</value>
        public bool Verified { get; set; }
    }
}
