using System;
using System.Reflection;
using System.Runtime.InteropServices;

#if RELEASE
[assembly: AssemblyConfiguration("Release")]
#else
[assembly: AssemblyConfiguration("Debug")]
#endif
[assembly: AssemblyCompany("Veritema")]
[assembly: AssemblyProduct("Veritema")]
[assembly: AssemblyCopyright("Copyright ©  2016")]
[assembly: AssemblyTrademark("")]
[assembly: AssemblyCulture("")]


// to COM components.  If you need to access a type in this assembly from
// COM, set the ComVisible attribute to true on that type.
[assembly: ComVisible(false)]
[assembly: CLSCompliant(false)]

// Setting ComVisible to false makes the

// Version information for an assembly consists of the following four values:
//
//      Major Version
//      Minor Version
//      Build Number
//      Revision
//
// You can specify all the values or you can default the Revision and Build Numbers
// by using the '*' as shown below:

[assembly: AssemblyVersion("1.0.*")]
//[assembly: AssemblyFileVersion("1.0.*")]