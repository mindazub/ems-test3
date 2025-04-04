<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Project') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="projectForm()">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <!-- Project Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Project Name</label>
                        <input type="text" name="name" class="form-input mt-1 block w-full" required>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" class="form-input mt-1 block w-full">
                    </div>

                    <!-- Companies -->
                    <template x-for="(company, companyIndex) in companies" :key="companyIndex">
                        <div class="border p-4 mb-4 rounded-md bg-gray-50">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-700">Company</h3>
                                <button type="button" class="text-red-600 text-sm"
                                    @click="removeCompany(companyIndex)">Remove</button>
                            </div>

                            <input type="text" :name="`companies[${companyIndex}][name]`" class="form-input mt-2 w-full"
                                placeholder="Company Name">

                            <!-- Plants -->
                            <template x-for="(plant, plantIndex) in company.plants" :key="plantIndex">
                                <div class="mt-4 ml-4 border-l-2 pl-4">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-sm font-medium text-gray-600">Plant</h4>
                                        <button type="button" class="text-red-500 text-xs"
                                            @click="removePlant(companyIndex, plantIndex)">Remove</button>
                                    </div>

                                    <input type="text" :name="`companies[${companyIndex}][plants][${plantIndex}][name]`"
                                        class="form-input mt-1 w-full" placeholder="Plant Name">

                                    <!-- Devices -->
                                    <template x-for="(device, deviceIndex) in plant.devices" :key="deviceIndex">
                                        <div class="mt-2 ml-4 border-l-2 pl-4">
                                            <div class="flex justify-between items-center">
                                                <label class="text-xs text-gray-500">Device</label>
                                                <button type="button" class="text-red-400 text-xs"
                                                    @click="removeDevice(companyIndex, plantIndex, deviceIndex)">Remove</button>
                                            </div>
                                            <input type="text"
                                                :name="`companies[${companyIndex}][plants][${plantIndex}][devices][${deviceIndex}][name]`"
                                                class="form-input mt-1 w-full" placeholder="Device Name">
                                        </div>
                                    </template>

                                    <button type="button" class="mt-2 text-blue-600 text-sm"
                                        @click="addDevice(companyIndex, plantIndex)">+ Add Device</button>
                                </div>
                            </template>

                            <button type="button" class="mt-2 text-blue-600 text-sm"
                                @click="addPlant(companyIndex)">+ Add Plant</button>
                        </div>
                    </template>

                    <button type="button" class="mb-4 text-blue-700 font-semibold" @click="addCompany()">+ Add Company</button>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create Project
                        </button>
                        <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js nested object -->
    <script>
        function projectForm() {
            return {
                companies: [],
                addCompany() {
                    this.companies.push({ name: '', plants: [] });
                },
                removeCompany(index) {
                    if (confirm('Remove this company?')) this.companies.splice(index, 1);
                },
                addPlant(companyIndex) {
                    this.companies[companyIndex].plants.push({ name: '', devices: [] });
                },
                removePlant(companyIndex, plantIndex) {
                    if (confirm('Remove this plant?')) this.companies[companyIndex].plants.splice(plantIndex, 1);
                },
                addDevice(companyIndex, plantIndex) {
                    this.companies[companyIndex].plants[plantIndex].devices.push({ name: '' });
                },
                removeDevice(companyIndex, plantIndex, deviceIndex) {
                    if (confirm('Remove this device?')) this.companies[companyIndex].plants[plantIndex].devices.splice(deviceIndex, 1);
                }
            }
        }
    </script>
</x-app-layout>
