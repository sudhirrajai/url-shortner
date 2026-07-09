<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(auth()->user()->isSuperAdmin())
                <span class="text-amber-600">Super Admin Panel</span>
                @elseif(auth()->user()->isAdmin())
                <span class="text-blue-600">Client Admin Dashboard ({{ auth()->user()->company->name ?? 'No Company' }})</span>
                @else
                <span class="text-green-600">Client Member Dashboard ({{ auth()->user()->company->name ?? 'No Company' }})</span>
                @endif
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Logged in as: <span class="font-semibold text-gray-800">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Success/Error Messages -->
            @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <ul class="list-disc list-inside text-sm text-rose-800">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- ==================== SUPER ADMIN VIEW ==================== -->
            @if(auth()->user()->isSuperAdmin())
            <!-- Clients Section -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 border-amber-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Clients</h3>
                        <button onclick="toggleModal('inviteClientModal')" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded transition duration-150">
                            Invite New Client
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client Name/ Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Company Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Generated URLs</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total URL Hits</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($clients as $client)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $client->client_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->users_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->short_urls_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $client->total_hits }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No client companies found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Global Short URLs -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 border-amber-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Generated Short URLs</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Short URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Long URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hits</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($urls as $url)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-amber-600">
                                        <a href="{{ route('short-urls.resolve', $url->short_code) }}" target="_blank" class="hover:underline">
                                            {{ route('short-urls.resolve', $url->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $url->original_url }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $url->hits }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $url->company->name ?? 'None' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No short URLs generated yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invite Client Modal -->
            <div id="inviteClientModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST" action="{{ route('invitations.store') }}">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Invite New Client</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input id="create_new_company" name="create_new_company" type="checkbox" checked onchange="toggleCompanyFields(this.checked)" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                                        <label for="create_new_company" class="ml-2 block text-sm text-gray-900 font-medium">Create a new company</label>
                                    </div>
                                    <div id="new_company_wrapper">
                                        <x-input-label for="new_company_name" value="New Company Name" />
                                        <x-text-input id="new_company_name" name="new_company_name" type="text" class="mt-1 block w-full" required />
                                    </div>
                                    <div id="existing_company_wrapper" class="hidden">
                                        <x-input-label for="company_id" value="Select Company" />
                                        <select id="company_id" name="company_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                            <option value="">-- Choose Company --</option>
                                            @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="email" value="Invite Email" />
                                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="role" value="Role" />
                                        <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                            <option value="admin">Admin</option>
                                            <option value="member">Member</option>
                                            <option value="sales">Sales</option>
                                            <option value="manager">Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-500 text-base font-semibold text-white hover:bg-amber-600 sm:ml-3 sm:w-auto sm:text-sm">
                                    Send Invitation
                                </button>
                                <button type="button" onclick="toggleModal('inviteClientModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- ==================== CLIENT ADMIN & MEMBER VIEW ==================== -->
            @if(auth()->user()->isAdmin() || auth()->user()->isMember() || auth()->user()->isSales() || auth()->user()->isManager())
            @php
            $isBlue = auth()->user()->isAdmin() || auth()->user()->isManager();
            @endphp

            <!-- Short URLs Section -->
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 {{ $isBlue ? 'border-blue-500' : 'border-green-500' }}">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Generated Short URLs</h3>
                        <button onclick="toggleModal('generateUrlModal')" class="{{ $isBlue ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-semibold py-2 px-4 rounded transition duration-150">
                            Generate
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Short URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Long URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hits</th>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created By</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($urls as $url)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $isBlue ? 'text-blue-600' : 'text-green-600' }}">
                                        <a href="{{ route('short-urls.resolve', $url->short_code) }}" target="_blank" class="hover:underline">
                                            {{ route('short-urls.resolve', $url->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $url->original_url }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $url->hits }}</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $url->user->name ?? 'Deleted User' }}</td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ (auth()->user()->isAdmin() || auth()->user()->isManager()) ? 4 : 3 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No short URLs created.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Team Members Section (Admin only) -->
            @if(auth()->user()->isAdmin())
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Team Members</h3>
                        <button onclick="toggleModal('inviteMemberModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-150">
                            Invite
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Generated URLs</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total URL Hits</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($teamMembers as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $member->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->urls_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->total_hits }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Invite Team Member Modal -->
            <div id="inviteMemberModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST" action="{{ route('invitations.store') }}">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Invite New Team Member</h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="email_member" value="Email" />
                                        <x-text-input id="email_member" name="email" type="email" class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="role_member" value="Role" />
                                        <select id="role_member" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                            <option value="member">Member</option>
                                            <option value="admin">Admin</option>
                                            <option value="sales">Sales</option>
                                            <option value="manager">Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-semibold text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                    Send Invitation
                                </button>
                                <button type="button" onclick="toggleModal('inviteMemberModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Generate URL Modal -->
            <div id="generateUrlModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form method="POST" action="{{ route('short-urls.store') }}">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Generate Short URL</h3>
                                <div>
                                    <x-input-label for="original_url" value="Long URL" />
                                    <x-text-input id="original_url" name="original_url" type="url" placeholder="e.g. https://google.com" class="mt-1 block w-full" required />
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $isBlue ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700' }} text-base font-semibold text-white sm:ml-3 sm:w-auto sm:text-sm">
                                    Generate
                                </button>
                                <button type="button" onclick="toggleModal('generateUrlModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Pending Invitations Link List (For easy interview testing/navigation) -->
            @if(in_array(auth()->user()->role, ['SuperAdmin', 'Admin']))
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-indigo-500">
                <h3 class="text-md font-bold text-gray-800 mb-4">Pending Invitation Links (For Interview Testing)</h3>
                <ul class="divide-y divide-gray-100">
                    @forelse(\App\Models\Invitation::whereNull('accepted_at')->get() as $invite)
                    <li class="py-3 flex justify-between items-center text-sm">
                        <div>
                            <span class="font-semibold text-gray-900">{{ $invite->email }}</span>
                            invited as
                            <span class="font-semibold text-gray-700">{{ $invite->role }}</span>
                            for
                            <span class="italic text-gray-600">{{ $invite->company->name ?? $invite->new_company_name }}</span>
                        </div>
                        <a href="{{ route('invitations.accept.form', $invite->token) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold underline">
                            Accept Link
                        </a>
                    </li>
                    @empty
                    <li class="py-2 text-gray-500 text-center">No pending invitations found.</li>
                    @endforelse
                </ul>
            </div>
            @endif

        </div>
    </div>

    <!-- Simple JavaScript for Modal Toggles -->
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        function toggleCompanyFields(createNew) {
            const newCompanyWrapper = document.getElementById('new_company_wrapper');
            const newCompanyInput = document.getElementById('new_company_name');
            const existingCompanyWrapper = document.getElementById('existing_company_wrapper');
            const existingCompanySelect = document.getElementById('company_id');

            if (createNew) {
                newCompanyWrapper.classList.remove('hidden');
                newCompanyInput.required = true;
                existingCompanyWrapper.classList.add('hidden');
                existingCompanySelect.required = false;
                existingCompanySelect.value = '';
            } else {
                newCompanyWrapper.classList.add('hidden');
                newCompanyInput.required = false;
                newCompanyInput.value = '';
                existingCompanyWrapper.classList.remove('hidden');
                existingCompanySelect.required = true;
            }
        }
    </script>
</x-app-layout>