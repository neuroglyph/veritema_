using System;
using System.Windows;
using System.Configuration;
using Google.Api.Maps.Service;
using Google.Api.Maps.Service.Geocoding;

namespace Veritema.Data
{
    public class GeocodingServiceEngine
    {
        public static void RequestMapData()
        {
             
            string address = String.IsNullOrEmpty() 
                ? string.Empty 
                : ConfigurationManager.AppSettings["VERITEMA_ADDRESS"].ToString();
       


        }
    }
}
