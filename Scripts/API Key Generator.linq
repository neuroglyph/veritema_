<Query Kind="Statements">
  <Namespace>System.Security.Cryptography</Namespace>
</Query>

Guid.Parse("a92c7e2d-e6cc-4d0f-a30f-86a4be1caecc").ToString().Replace("-",string.Empty).Dump("AppId");
using (var cryptoProvider = new RNGCryptoServiceProvider())
{
	byte[] secretKeyByteArray = new byte[32]; //256 bit
	cryptoProvider.GetBytes(secretKeyByteArray);
	Convert.ToBase64String(secretKeyByteArray).Dump("API Key");
}