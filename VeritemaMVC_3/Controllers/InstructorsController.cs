using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using Veritema.Data;
using VeritemaMVC_3.ViewModels;

namespace VeritemaMVC_3.Controllers
{
    public class InstructorsController : ApiController
    {
        // GET api/<controller>
        /// <summary>
        /// 10.05.2016 (RG)
        /// Returns all instructors (currently used in Classes Index view).
        /// Ultimately, these will be stored in a database - for now, just hard-code
        /// </summary>
        /// <returns></returns>5
        public IEnumerable<Instructor> Get()
        {
            return new List<Instructor>
            {
                new Instructor
                {
                    ID = 1,
                    FirstName = "John",
                    LastName = "Taube",
                    Style =  null,
                    Description = "John Taube is the school's Founder and Soke of Seido-Ryu Jitsu. 6th Dan (Hapkido - Taekwondo - Jujitsu); 3rd Dan Judo; Kru Muay Thai;  Brown Belt RCJ Machado Brazilian Jiu-Jitsu",
                    ImageUrl = "/images/JohnTaube.JPG"
                },
                new Instructor
                {
                  ID = 2,
                   FirstName = "Chris",
                   LastName = "Tricoli",
                   Style = null,
                   Description = "Chris Tricoli has owned and operated martial arts schools for over twenty-one years. He has had the rare privilege to represent such awesome lineage such a, Sifu Jeffery Gay, William White, Carlos Machado, Royce Gracie, Scott Francis, Professor Ernie Cates, GM Cyrus, GM Jae Han Ji, GM In Sun Seo, GM West, Dr. Kimm, and Master John Taube",
                   ImageUrl = "/images/chris_tricoli.jpg"
                },
                new Instructor
                {
                    ID = 3,
                    FirstName = "Ben ",
                    LastName = "Tice",
                    Style = MartialArtStyle.Hapkido,
                    Description = "More Info Coming Soon!",
                    ImageUrl = "/images/site/equip-joomla-fitness-theme-logo.png", // the default image
                },
                new Instructor
                {
                    ID = 4,
                    FirstName = "Eric",
                    LastName = "Swanson",
                    Style = MartialArtStyle.Hapkido,
                    Description = "More Info Coming Soon!",
                    ImageUrl = "/images/site/equip-joomla-fitness-theme-logo.png", // the default image

                }
            };
        }

        // GET api/<controller>/5
        public string Get(int id)
        {
            return "value";
        }

        // POST api/<controller>
        public void Post([FromBody]string value)
        {
        }

        // PUT api/<controller>/5
        public void Put(int id, [FromBody]string value)
        {
        }

        // DELETE api/<controller>/5
        public void Delete(int id)
        {
        }
    }
}