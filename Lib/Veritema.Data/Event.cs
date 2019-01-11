using System;
using System.Diagnostics;

namespace Veritema.Data
{
    /// <summary>
    /// Represents a scheduled item
    /// </summary>
    [DebuggerDisplay("{DebuggerDisplay}")]
    public class Event
    {
        /// <summary>
        /// Gets or sets a value indicating whether this <see cref="Event"/> is pending confirmation.
        /// </summary>
        /// <value><c>true</c> if pending; otherwise, <c>false</c>.</value>
        public bool Confirmed { get; set; }

        /// <summary>
        /// Gets or sets the UTC datetime in which the event starts.
        /// </summary>
        /// <value>The end UTC.</value>
        public DateTime EndUtc { get; set; }

        private string DebuggerDisplay => $"[{Id}] {Title}/{Description} from {StartUtc} -> {EndUtc} @ {Location}";

        /// <summary>
        /// Gets or sets the a textual description of the event.
        /// </summary>
        /// <value>The description.</value>
        public string Description { get; set; }

        /// <summary>
        /// Gets or sets the event identifier.
        /// </summary>
        /// <value>The identifier.</value>
        public Int64 Id { get; set; }

        /// <summary>
        /// Gets or sets the location where the event occurs.
        /// </summary>
        /// <value>The location.</value>
        public Location Location { get; set; }

        /// <summary>
        /// Gets or sets the UTC datetime in which the event starts.
        /// </summary>
        /// <value>The start UTC.</value>
        public DateTime StartUtc { get; set; }

        /// <summary>
        /// Gets or sets the Martial Arts Style property.
        /// </summary>
        public MartialArtStyle? Style { get; set; } = new MartialArtStyle?();

        /// <summary>
        /// Gets or sets the title of the event.
        /// </summary>
        /// <value>The title.</value>
        public string Title { get; set; }

        /// <summary>
        /// Gets or sets the type of the event.
        /// </summary>
        /// <value>The type.</value>
        public EventType Type { get; set; }

        /// <summary>
        /// Gets or sets epoch when this event was updated.
        /// </summary>
        /// <value>The updated.</value>
        public DateTimeOffset Updated { get; set; }
    }
}
