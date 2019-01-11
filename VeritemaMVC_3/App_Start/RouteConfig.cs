using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.Routing;

namespace VeritemaMVC_3
{
    public class RouteConfig
    {
        public static void RegisterRoutes(RouteCollection routes)
        {
            routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

            routes.MapMvcAttributeRoutes();

            routes.MapRoute(
                name: "Default",
                url: "{controller}/{action}/{id}",
                defaults: new { controller = "Home", action = "Index", id = UrlParameter.Optional }
            );

            /*
            C:\projects\VeritemaMVC_3\VeritemaMVC_3\components\com_rsmediagallery\assets\gallery


            routes.MapRoute(
                name: "images",
                url: "{folder}/{components}/{com_rsmediagallery}/{assets}",
                defaults: new { controller = "Home", action = "Images" }
            );
            */
        }
    }
}
