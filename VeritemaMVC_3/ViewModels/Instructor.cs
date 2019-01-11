using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using Veritema.Data;

namespace VeritemaMVC_3.ViewModels
{



    public class Instructor
    {
        public int ID { get; set; }
        public string FirstName { get; set; }

        public string LastName { get; set; }

        /// <summary>
        /// Type of martial arts practiced (Hapkido, Judo, etc)
        /// </summary>
        public MartialArtStyle? Style { get; set; }

        public string Description { get; set;}

        public string ImageUrl { get; set; }
    }
}