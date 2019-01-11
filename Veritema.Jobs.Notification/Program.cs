using Microsoft.Azure.WebJobs;
using Microsoft.Practices.Unity;
using Veritema.Jobs.Notification.Composition;

namespace Veritema.Jobs.Notification
{
    // To learn more about Microsoft Azure WebJobs SDK, please see http://go.microsoft.com/fwlink/?LinkID=320976
    class Program
    {
        // Please set the following connection strings in app.config for this WebJob to run:
        // AzureWebJobsDashboard and AzureWebJobsStorage
        static void Main()
        {
            var container = new UnityContainer();
            var bootstrapper = new DependencyBootstrapper();
            bootstrapper.Configure(container);
            var activator = new UnityJobActivator(container);
            var host = new JobHost(new JobHostConfiguration { JobActivator = activator });
            // The following code ensures that the WebJob will be running continuously
            host.RunAndBlock();
        }
    }
}
