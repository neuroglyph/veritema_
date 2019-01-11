using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;

namespace VeritemaMVC_3.Controllers
{
    public class SpecialtiesController : Controller
    {
        // GET: Specialties
        public ActionResult Index()
        {
            return View();
        }

        public ActionResult LawEnforcement()
        {
            return View();
        }

        public ActionResult WeaponsTraining()
        {
            return View();
        }

        public ActionResult ProtectionTraining()
        {
            return View();
        }

        public ActionResult Seminars()
        {
            return View();
        }

        public ActionResult PrivateLessons()
        {
            return View();
        }
    }
}