using System;
using System.Diagnostics;

namespace Veritema.Data
{
    /// <summary>   
    /// A POCO to project the database record
    /// </summary>
    [DebuggerDisplay("{DebuggerDisplay}")]
    public class EventRecord : IEquatable<EventRecord>
    {
        public bool Confirmed { get; set; }

        private string DebuggerDisplay => $"[{Id}] {Title}/{Details} from {Start} -> {End} @ {LocationId}";

        public string Details { get; set; }
        public DateTimeOffset End { get; set; }
        public Int64 Id { get; set; }
        public int? LocationId { get; set; }
        public DateTimeOffset Start { get; set; }
        public int? StyleId { get; set; }
        public string Title { get; set; }
        public char TypeId { get; set; }
        public DateTimeOffset Updated { get; set; }

        /// <summary>
        /// Indicates whether the current object is equal to another object of the same type.
        /// </summary>
        /// <param name="other">An object to compare with this object.</param>
        /// <returns>true if the current object is equal to the <paramref name="other" /> parameter; otherwise, false.</returns>
        public bool Equals(EventRecord other)
        {
            bool equals = false;
            if (other != null)
            {
                equals = Confirmed == other.Confirmed &&
                         Details == other.Details &&
                         End == other.End &&
                         Id == other.Id &&
                         LocationId == other.LocationId &&
                         Start == other.Start &&
                         StyleId == other.StyleId &&
                         Title == other.Title &&
                         TypeId == other.TypeId;
            }
            return equals;
        }
    }
}
