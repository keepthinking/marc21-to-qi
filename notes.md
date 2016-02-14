# Conversion notes

2016-02-18

Compared to the previous version:
- Added tag="852 (Location) in Location tab. This included two new lists: Location Collection and Location Section
- Added tag="884 (Conversion)


700.4
--------

Relationship code. Is this part of the authority (actor) or the Relationship?
Ignored for now

884.h
--------
This is not a valid MARC21 identifier: https://www.loc.gov/marc/bibliographic/bd884.html

887
--------
I would not know how to treat this:

<!--Non-MARC Information Field: record created by-->
<datafield tag="887" ind1=" " ind2=" ">
	<subfield code="a">conversion</subfield>
	<subfield code="2">http://webi.provant.be/brocade/catalog/catxml.dtd
		TSECTION/@cp</subfield>
</datafield>
<!--Non-MARC Information Field: record last modified by-->
<datafield tag="887" ind1=" " ind2=" ">
	<subfield code="a">conversion</subfield>
	<subfield code="2"> http://webi.provant.be/brocade/catalog/catxml.dtd
		TSECTION/@mp</subfield>
</datafield>
<!--Non-MARC Information Field: record creation date, rounded to the month-->
<datafield tag="887" ind1=" " ind2=" ">
	<subfield code="a">20010701000000.0</subfield>
	<subfield code="2"> http://webi.provant.be/brocade/catalog/catxml.dtd
		TSECTION/@cd</subfield>
</datafield>
<!--Non-MARC Information Field: publication start date as sort date (format YYYY)-->
<datafield tag="887" ind1=" " ind2=" ">
	<subfield code="a">1979</subfield>
	<subfield code="2"> http://webi.provant.be/brocade/catalog/catxml.dtd
		BSECTION/IM/JU/@ju1sv </subfield>
</datafield>
<!--Non-MARC Information Field: publication end date as sort date (format YYYY)-->
<datafield tag="887" ind1=" " ind2=" ">
	<subfield code="a"/>
	<subfield code="2"> http://webi.provant.be/brocade/catalog/catxml.dtd
		BSECTION/IM/JU/@ju2sv </subfield>
</datafield>

It does look all the same, but it should have different meanings. I believe this data should be different.