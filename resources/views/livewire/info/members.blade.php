@php
       $authIsAdminInGroup=  $participant?->isAdmin();
       $authIsOwner=  $participant?->isOwner();
       $isGroup=  $conversation?->isGroup();

    @endphp


<div x-ref="members"
    class="h-[calc(100vh_-_6rem)]  sm:h-[450px] bg-white dark:bg-gray-800 dark:text-white border dark:border-gray-700 overflow-y-auto overflow-x-hidden  ">

    <header class=" sticky top-0 bg-white  dark:bg-gray-800 z-10 p-2">
        <div class="flex items-center justify-center pb-2">

            <button wire:click="$dispatch('closeModal')" dusk="close_modal_button"
                class="p-2 ml-0 text-gray-600 hover:dark:bg-gray-700 hover:dark:text-white rounded-full hover:text-gray-800 hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class=" w-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>

            </button>

            <h3 class=" mx-auto font-semibold ">@lang('Members')</h3>



        </div>

        {{-- Member limit error --}}
        <section class="flex flex-wrap items-center px-0 border-b dark:border-gray-700">
            <input type="search" id="users-search-field" wire:model.live.debounce='search' autocomplete="off"
                placeholder="{{ __('Search Members') }}"
                class=" w-full border-0 w-auto dark:bg-gray-800 outline-none focus:outline-none bg-none rounded-lg focus:ring-0 hover:ring-0">
        </section>

    </header>


    <div class="relative w-full p-2 ">
        {{-- <h5 class="text font-semibold text-gray-800 dark:text-gray-100">Recent Chats</h5> --}}
        <section class="my-4">
            @if ($participants)

                <ul class="overflow-auto flex flex-col">

                    @foreach ($participants as $key => $participant)
                        @php
                            $loopParticipantIsAuth =
                                $participant->participantable_id == auth()->id() &&
                                $participant->participantable_type == auth()->user()->getMorphClass();
                        @endphp
                        <li x-data="{ open: false }" x-ref="button" @click="open = ! open" x-init="$watch('open', value => {
                            $refs.members.style.overflow = value ? 'hidden' : '';
                        })"
                            @click.away ="open=false;" wire:key="users-{{ $key }}"
                            :class="!open || 'bg-gray-100 dark:bg-gray-900/40'"
                            class="flex cursor-pointer group gap-2 items-center overflow-x-hidden p-2 py-3">

                            <label class="flex cursor-pointer gap-2 items-center w-full">
                                <x-wirechat::avatar src="{{ $participant->participantable->cover_url }}"
                                    class="w-10 h-10" />

                                <div class="grid grid-cols-12 w-full ">
                                    <h6 @class(['transition-all truncate group-hover:underline col-span-10' ])>
                                        {{ $loopParticipantIsAuth ? __('You') : $participant->participantable->display_name }}</h6>
                                        @if ($participant->isOwner()|| $participant->isAdmin())
                                        <span  style="background-color: var(--primary-color);" class=" flex items-center col-span-2 text-white text-xs font-medium ml-auto px-2.5 py-px rounded ">
                                            {{ __($participant->isOwner() ? 'Owner' : 'Admin') }}
                                        </span>
                                        @endif

                                </div>

                                <div x-show="open" x-anchor.bottom-end="$refs.button"
                                    class="ml-auto bg-gray-50 dark:bg-gray-800 dark:border-gray-600 py-4 shadow border rounded-md grid space-y-2 w-52">
                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-600 dark:text-gray-300  w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>   --}}

                                    <x-wirechat::dropdown-button wire:click="sendMessage('{{ $participant->id }}')"
                                        class="truncate dark:hover:bg-gray-700 dark:focus:bg-gray-700">
                                        @lang('Message')
                                        {{ $loopParticipantIsAuth ? __('Yourself') : $participant->participantable->display_name }}
                                    </x-wirechat::dropdown-button>

                                    @if ($authIsAdminInGroup || $authIsOwner)
                                        {{-- Only show admin actions to owner of group and if is not the current loop --}}
                                        {{--AND We only want to show admin actions if participant is not owner --}}
                                        @if ($authIsOwner && !$loopParticipantIsAuth)
                                            @if ($participant->isAdmin())
                                                <x-wirechat::dropdown-button
                                                    wire:click="dismissAdmin('{{ $participant->id }}')"
                                                    wire:confirm="{{ __('Are you sure you want to dismiss :name as Admin ?', ['name' => $participant->participantable?->display_name]) }}"
                                                    class=" dark:hover:bg-gray-700 ">
                                                    @lang('Dismiss As Admin')
                                                </x-wirechat::dropdown-button>
                                            @else
                                                <x-wirechat::dropdown-button
                                                    wire:click="makeAdmin('{{ $participant->id }}')"
                                                    wire:confirm="{{ __('Are you sure you want to make :name an Admin ?', ['name' => $participant->participantable?->display_name]) }}"
                                                    class=" dark:hover:bg-gray-700 ">
                                                    @lang('Make Admin')
                                                </x-wirechat::dropdown-button>
                                            @endif
                                        @endif

                                                {{--AND We only want to show remove actions if participant is not owner of conversation because we don't want to remove owner--}}
                                                @if (!$participant->isOwner() && !$loopParticipantIsAuth && !$participant->isAdmin())
                                                <x-wirechat::dropdown-button
                                                    wire:click="removeFromGroup('{{ $participant->id }}')"
                                                    wire:confirm="{{ __('Are you sure you want to remove :name from Group ?', ['name' => $participant->participantable?->display_name]) }}"
                                                    class="text-red-500 dark:text-red-500 dark:hover:bg-gray-700">
                                                    @lang('Remove')
                                                </x-wirechat::dropdown-button>
                                                @endif

                                    @else
                                    @endif



                                </div>
                            </label>

                        </li>
                    @endforeach



                </ul>


                {{-- Load more button --}}
                @if ($canLoadMore)
                    <section class="w-full justify-center flex my-3">
                        <button dusk="loadMoreButton" @click="$wire.loadMore()"
                            class=" text-sm dark:text-white hover:text-gray-700 transition-colors dark:hover:text-gray-500 dark:gray-200">
                            @lang('Load more')
                        </button>
                    </section>
                @endif
            @endif

        </section>
    </div>

</div>
