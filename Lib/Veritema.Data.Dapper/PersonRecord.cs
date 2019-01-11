using System;

namespace Veritema.Data
{
    /// <summary>
    /// The backing store layout of the <see cref="Person"/>
    /// </summary>
    public class PersonRecord
    {
        /// <summary>
        /// Gets or sets the timestamp when the record was created.
        /// </summary>
        /// <value>The created.</value>
        public DateTimeOffset Created { get; set; }

        /// <summary>
        /// Gets or sets the record identifier.
        /// </summary>
        /// <value>The identifier.</value>
        public int Id { get; set; }

        /// <summary>
        /// Gets or sets the updated.
        /// </summary>
        /// <value>The updated.</value>
        public DateTimeOffset Updated { get; set; }

        /// <summary>
        /// Gets or sets the JSON representation of the person.
        /// </summary>
        /// <value>The value.</value>
        public string Value { get; set; }
    }
}
