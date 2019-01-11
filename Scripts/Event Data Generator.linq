<Query Kind="Program">
  <Reference Relative="..\Lib\Veritema.Data.Dapper\bin\Debug\Veritema.Data.Dapper.dll">C:\Projects\VSTS\Veritema\Lib\Veritema.Data.Dapper\bin\Debug\Veritema.Data.Dapper.dll</Reference>
  <Reference Relative="..\Lib\Veritema.Data.Dapper\bin\Debug\Veritema.Data.dll">C:\Projects\VSTS\Veritema\Lib\Veritema.Data.Dapper\bin\Debug\Veritema.Data.dll</Reference>
  <NuGetReference>Dapper</NuGetReference>
  <NuGetReference>Dapper.Contrib</NuGetReference>
  <Namespace>Dapper</Namespace>
  <Namespace>Dapper.Contrib.Extensions</Namespace>
  <Namespace>Veritema.Data</Namespace>
</Query>

private static Dictionary<Type, string> EntityMapper;
private static Dictionary<DayOfWeek, Initializer> InitializerFactory;

async void Main()
{
    EntityMapper = new Dictionary<Type, string>
	{
		[typeof(EventRecord)] = "v.Event"
	};

	InitializerFactory = new Dictionary<DayOfWeek, Initializer>
	{
		[DayOfWeek.Monday] = new Initializer { Details = "Traditional Hapkido", Style = MartialArtStyle.Hapkido },
		[DayOfWeek.Tuesday] = new Initializer { Details = "No GI JiuJitsu", Style = MartialArtStyle.JiuJitsu },
		[DayOfWeek.Wednesday] = new Initializer { Details = "Yudo", Style = MartialArtStyle.Judo },
		[DayOfWeek.Thursday] = new Initializer { Details = "Traditional Hapkido", Style = MartialArtStyle.Hapkido },
	};
    SqlMapperExtensions.TableNameMapper = type => EntityMapper[type];

    var offset = DateTime.Now.AddMonths(-1);
    DateTime start = new DateTime(offset.Year, offset.Month, 1);

    using (var connection = new SqlConnection("Data Source=.;Initial Catalog=veritema;Persist Security Info=False;Integrated Security=SSPI;MultipleActiveResultSets=False;Connection Timeout=30;"))
	
    {
        Enumerable.Range(0, 180)
              .Select(i => start.AddDays(i))
              .Where(i => i.DayOfWeek >= DayOfWeek.Monday && i.DayOfWeek < DayOfWeek.Friday)
              .Select(i => InitializerFactory[i.DayOfWeek].On(i))
              .ToList()
			  .ForEach(i => connection.Insert(i));
    }
}

private class Initializer
{
	public string Details { get; set; }
	public MartialArtStyle? Style { get; set; } = new MartialArtStyle?();

	public EventRecord On(DateTime date)
	{
		var record = new EventRecord();
		record.Title = "Hapkido Track";
		record.Details = Details;
		record.Start = new DateTimeOffset(date.Year, date.Month, date.Day, 18, 30, 0, DateTimeOffset.Now.Offset);
		record.End = record.Start.AddHours(1);
		record.StyleId = Style.HasValue ? (int)Style.Value : new int?();
		record.LocationId = 1;
		return record;
	}
}