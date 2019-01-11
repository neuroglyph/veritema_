using System;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using Google.Api.Maps.Service.Geocoding;
using Google.Api.Maps.Service.StaticMaps;

namespace VeritemaMVC_3
{
    public class BundleConfig
    {
        public static void RegisterBundles(BundleCollection bundles)
        {
            // Use the development version of Modernizr to develop with and learn from. Then, when you're
            // ready for production, use the build tool at http://modernizr.com to pick only the tests you need.
            bundles.Add(new ScriptBundle("~/bundles/modernizr").Include(
                        "~/Scripts/modernizr-*"));


            bundles.Add(new ScriptBundle("~/bundles/knockout").Include(
                      "~/Scripts/knockout/viewmodels.js",
                       "~/Scripts/knockout/knockout-2.2.0.js"));

        }
    }
}