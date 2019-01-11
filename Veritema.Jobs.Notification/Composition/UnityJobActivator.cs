using System;
using Microsoft.Azure.WebJobs.Host;
using Microsoft.Practices.Unity;

namespace Veritema.Jobs.Notification.Composition
{
    /// <summary>
    /// A <see cref="IJobActivator"/> utilizing Unity
    /// </summary>
    /// <seealso cref="Microsoft.Azure.WebJobs.Host.IJobActivator" />
    public class UnityJobActivator : IJobActivator
    {
        private readonly IUnityContainer _container;

        /// <summary>
        /// Initializes a new instance of the <see cref="UnityJobActivator"/> class.
        /// </summary>
        /// <param name="container">The container.</param>
        /// <exception cref="System.ArgumentNullException">container</exception>
        public UnityJobActivator(IUnityContainer container)
        {
            if (container == null)
            {
                throw new ArgumentNullException(nameof(container));
            }
            _container = container;            
        }

        /// <summary>
        /// Creates a new instance of a job type.
        /// </summary>
        /// <typeparam name="T">The job type.</typeparam>
        /// <returns>A new instance of the job type.</returns>
        public T CreateInstance<T>() => _container.Resolve<T>();
    }
}
