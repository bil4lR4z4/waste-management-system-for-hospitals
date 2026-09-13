<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hospital Profile</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #000;
        }

        h2, h5 {
            color: #2c3e50;
        }

        .top{
            margin-bottom:15px;
            border-bottom:2px solid #2c3e50;
            padding-bottom:10px;
        }

        .top table{
            width:100%;
            border-collapse:collapse;
            border:none;
        }

        .top td{
            padding:7px;
            border:none; 
        }


        .top img{
            width:130px;
        }

        .top .name{
            font-size:22px;
            font-weight:bold;
            color:#2c3e50;
        }

        .section {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .label {
            width: 35%;
            background: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="top">
        <table width="100%" border="0">
            <tr>
                <td width="20%" align="left">
                    @if($setting->logo)
                        <img src="{{ public_path($setting->logo) }}">
                    @endif
                </td>

                <td width="80%" align="center">
                    <div class="name">{{ $setting->hospital_name }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 style="margin-bottom: 10px;">Basic Information</h3>
        <table>
            <tr>
                <td class="label">Company Name</td>
                <td>{{ $hospital->company_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Owner Name</td>
                <td>{{ $hospital->owner_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $hospital->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Registration Number</td>
                <td>{{ $hospital->registration_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NTN</td>
                <td>{{ $hospital->ntn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">PHC</td>
                <td>{{ $hospital->phc ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">PRA</td>
                <td>{{ $hospital->pra ?? '-' }}</td>
            </tr>
            <!-- <tr>
                <td class="label">SECP</td>
                <td>{{ $hospital->secp ?? '-' }}</td>
            </tr> -->
            <tr>
                <td class="label">Contact Number</td>
                <td>{{ $hospital->contact_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Registration Date</td>
                <td>{{ $hospital->date ? \Carbon\Carbon::parse($hospital->date)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Expiry Date</td>
                <td>{{ $hospital->expiry_date ? \Carbon\Carbon::parse($hospital->expiry_date)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Payment</td>
                <td>{{ $hospital->payment ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">City</td>
                <td>{{ $hospital->city ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Address</td>
                <td>{{ $hospital->address ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Facilities</td>
                <td> 
                    @php
                        $facilities = explode(',', $hospital->facilities ?? '');
                    @endphp
                    {{ !empty($facilities) ? implode(', ', $facilities) : '-' }}
                </td>
            </tr>
        </table>
    </div>


    <!-- Facility Details -->
    <div class="section">
        <h3 style="margin-bottom: 10px;">Facility Details</h3>
        <table>
            <tr>
                <td class="label">Number of Beds</td>
                <td>{{ $hospital->number_of_beds ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Waste Generation Volume (kg/day)</td>
                <td>{{ $hospital->waste_generation_volume ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Type of Waste Generated</td>
                <td>
                    @php
                        $wasteTypes = explode(',', $hospital->waste_type ?? '');
                    @endphp
                    {{ !empty($wasteTypes) ? implode(', ', $wasteTypes) : '-' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 style="margin-bottom: 10px;">Waste Collection Information</h3>
        <table>
            <tr>
                <td class="label">Preferred Waste Collection Frequency</td>
                <td>{{ $hospital->preferred_waste_collection_frequency ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Temporary Waste Storage</td>
                <td>{{ $hospital->has_temporary_waste_storage == 'yes' ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td class="label">Waste Storage Method</td>
                <td>{{ $hospital->waste_storage_method ?? '-' }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
