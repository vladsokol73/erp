import { useState, useMemo, useEffect } from "react";
import AppLayout from "@/components/layouts/app-layout";
import InputSearch from "@/components/ui/input-search";
import Lightbox from "yet-another-react-lightbox";
import Video from 'yet-another-react-lightbox/plugins/video';
import "yet-another-react-lightbox/styles.css";

import {
    ImageSlide,
    VideoSlide,
    CreativeData,
    CountryCreative,
    UserCreative,
    TagCreative,
    FilterValues,
    CommentCreative,
} from "@/components/library/types";

// Импортируем компоненты библиотеки
import {
    FilterPanel,
    SortDropdown,
    MediaCard
} from "@/components/library";

import {TablePagination} from "@/components/ui/table-pagination";
import MediaCardSkeleton from "@/components/library/media-card-skeleton";

import { useInertiaUrlState } from "@/hooks/use-inertia-url-state";
import useApi from '@/hooks/use-api';
import { route } from 'ziggy-js';
import {Head} from "@inertiajs/react";
import PerPageDropdown from "@/components/library/per-page-dropdown";
import {usePerPageStore} from "@/store/usePerPageStore";

type Slide = ImageSlide | VideoSlide;

interface Props {
    creatives: App.DTO.PaginatedListDto<App.DTO.Creative.CreativeListDto>,
    creatives_countries: CountryCreative[],
    creatives_users: UserCreative[],
    creatives_tags: TagCreative[],
    tags: TagCreative[],
}

export default function LibraryPage({creatives: initialCreatives, creatives_countries, creatives_users, creatives_tags, tags}: Props) {

    const api = useApi();
    const [creatives, setCreatives] = useState<App.DTO.PaginatedListDto<App.DTO.Creative.CreativeListDto>>(initialCreatives);

    useEffect(() => {
        setCreatives(initialCreatives);
    }, [initialCreatives]);

    const perPage = usePerPageStore((s) => s.getPerPage('creatives', 16));

    // Состояния
    const [filters, setFilters, submitFilters, resetFilters, loadingFilters] = useInertiaUrlState(
        {
            filter: {
                countries: [],
                users: [],
                tags: [],
                types: []
            },
            search: '',
            sort: 'date_desc',
            page: 1,
            perPage: perPage
        },
        {
            omitDefaults: ['search', 'sort', 'page', 'filters'],
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true
            }
        }
    );

    const [openImagePreview, setOpenImagePreview] = useState(false);
    const [currentSlideIndex, setCurrentSlideIndex] = useState(0);

    // Слайды для лайтбокса
    const slides: Slide[] = useMemo(() => {
        return creatives.items.map(item => {
            // Проверяем тип медиа и создаем соответствующий слайд
            if (item.type === 'video') {
                return {
                    type: "video",
                    sources: [
                        {
                            src: item.url,
                            type: "video/mp4"
                        }
                    ]
                } as VideoSlide;
            } else {
                return {
                    src: item.url
                } as ImageSlide;
            }
        });
    }, [creatives.items]);

    // Обработчики событий
    const handleSortChange = (sort: string) => {
        setFilters({ sort: sort });
    };

    const handlePerPageChange = (page: number) => {
        usePerPageStore.getState().setPerPage('creatives', page);
        setFilters({ perPage: page });
    };

    const handleAddComment = (creativeId: number, comment: string) => {
        if (!comment.trim()) {
            return;
        }

        // Отправляем запрос на сервер
        api.post(
            route('creatives.library.comments', {creativeId: creativeId}),
            {
                comment: comment
            },
            {
                onSuccess: (data) => {
                    // При успешном ответе обновляем состояние с реальными данными от сервера
                    const realComment: CommentCreative = data.comment;

                    setCreatives(prevState => {
                        const updatedItems = prevState.items.map(item => {
                            if (item.id === creativeId) {
                                return {
                                    ...item,
                                    comments: [realComment, ...item.comments]
                                };
                            }
                            return item;
                        });

                        return {
                            ...prevState,
                            items: updatedItems
                        };
                    });
                },
                onError: () => {

                }
            }
        ).then();
    };

    const updateCreativeReaction = (creativeId: number, reactionType: 'like' | 'dislike') => {
        setCreatives(prevState => {
            const updatedItems = prevState.items.map(item => {
                if (item.id === creativeId) {
                    const updatedItem = { ...item };

                    if (reactionType === 'like') {
                        // Если уже есть лайк - снимаем его
                        if (updatedItem.user_liked) {
                            updatedItem.likes_count = Math.max(0, updatedItem.likes_count - 1);
                            updatedItem.user_liked = false;
                        }
                        // Иначе ставим лайк и снимаем дизлайк если был
                        else {
                            updatedItem.likes_count += 1;
                            updatedItem.user_liked = true;

                            if (updatedItem.user_disliked) {
                                updatedItem.user_disliked = false;
                                updatedItem.dislikes_count = Math.max(0, updatedItem.dislikes_count - 1);
                            }
                        }
                    } else if (reactionType === 'dislike') {
                        // Если уже есть дизлайк - снимаем его
                        if (updatedItem.user_disliked) {
                            updatedItem.dislikes_count = Math.max(0, updatedItem.dislikes_count - 1);
                            updatedItem.user_disliked = false;
                        }
                        // Иначе ставим дизлайк и снимаем лайк если был
                        else {
                            updatedItem.dislikes_count += 1;
                            updatedItem.user_disliked = true;

                            if (updatedItem.user_liked) {
                                updatedItem.user_liked = false;
                                updatedItem.likes_count = Math.max(0, updatedItem.likes_count - 1);
                            }
                        }
                    }

                    return updatedItem;
                }
                return item;
            });

            return {
                ...prevState,
                items: updatedItems
            };
        });
    };

    const updateCreativeBookmark = (creativeId: number) => {
        setCreatives(prevState => {
            const updatedItems = prevState.items.map(item => {
                if (item.id === creativeId) {
                    const updatedItem = { ...item };

                    updatedItem.favorite = !updatedItem.favorite;

                    return updatedItem;
                }
                return item;
            });

            return {
                ...prevState,
                items: updatedItems
            };
        });
    };

    const handleLike = (creativeId: number) => {
        updateCreativeReaction(creativeId, 'like');

        api.post(
            route('creatives.library.reactions', {creativeId: creativeId}),
            {
                type: 'like'
            },
            {
                onError: () => {
                    updateCreativeReaction(creativeId, 'like');
                }
            }
        ).then();
    };

    const handleDislike = (creativeId: number) => {
        updateCreativeReaction(creativeId, 'dislike');

        api.post(
            route('creatives.library.reactions', {creativeId: creativeId}),
            {
                type: 'dislike'
            },
            {
                onError: () => {
                    updateCreativeReaction(creativeId, 'dislike');
                }
            }
        ).then();
    };

    const handleDownload = (id: number, type: "original" | "unique") => {
        const creative = creatives.items.find(item => item.id === id);

        if (type === "original") {
            const creative = creatives.items.find(item => item.id === id);

            if (!creative) {
                return;
            }

            const downloadUrl = creative.url;

            window.open(downloadUrl, '_blank');
        } else {

        }
    };
    const handleOpenPreview = (index: number) => {
        setCurrentSlideIndex(index);
        setOpenImagePreview(true);
    };

    const handlePageChange = (page: number) => {
        setFilters({ page: page });
    }

    const handleSearchChange = (search: string) => {
        setFilters({ search: search });
    }

    const handleFilterChange = (newFilters: FilterValues): void => {
        setFilters({
            filter: {
                countries: newFilters.countries as never[],
                users: newFilters.users as never[],
                tags: newFilters.tags as never[],
                types: newFilters.types as never[],
            }
        });
        setFilters({
            page: 1
        });
    };

    const handleSetFavorite = (creativeId: number, favorite: boolean) => {
        updateCreativeBookmark(creativeId);

        api.post(
            route('creatives.library.favorites', {creativeId: creativeId}),
            {
                type: favorite ? 'favorite' : 'unfavorite'
            },
            {
                onError: () => {
                    updateCreativeBookmark(creativeId);
                }
            }
        ).then();
    }

    const handleSaveCreative = async (creativeId: number, selectedTags: TagCreative[]) => {
        await api.put(
            route("creatives.library.tags.update", { creativeId }),
            {
                tags: selectedTags.map(tag => tag.id),
            },
            {
                onSuccess: () => {
                    setCreatives(prevState => {
                        const updatedItems = prevState.items.map(item => {
                            if (item.id === creativeId) {
                                return {
                                    ...item,
                                    tags: selectedTags,
                                };
                            }
                            return item;
                        });

                        return {
                            ...prevState,
                            items: updatedItems,
                        };
                    });
                },
                onError: () => {
                    throw new Error("Ошибка при сохранении тегов");
                },
            }
        );
    };

    const handleDeleteCreative = async (creativeId: number) => {
        await api.delete(
            route("creatives.library.delete", { creativeId }),
            {
                onSuccess: () => {
                    setFilters({ page: filters.page });
                },
                onError: () => {
                    throw new Error("Ошибка при сохранении тегов");
                },
            }
        );
    }


    return (
        <AppLayout>
            {/* Лайтбокс для предпросмотра изображений и видео */}
            <Lightbox
                open={openImagePreview}
                close={() => setOpenImagePreview(false)}
                controller={{
                    closeOnBackdropClick: true,
                    closeOnPullDown: true,
                }}
                plugins={[Video]}
                slides={slides}
                index={currentSlideIndex}
                video={{
                    autoPlay: true,
                    controls: true
                }}
            />

            <Head title="Library" />
            <h1 className="text-2xl font-bold mb-4">
                Library
            </h1>

            {/* Секция поиска и фильтрации */}
            <div className="flex flex-col gap-4 mb-4">
                <InputSearch
                    defaultValue={filters.search}
                    onChangeDebounced={handleSearchChange}
                />

                <FilterPanel
                    countries={
                        creatives_countries.map(
                            (country) => ({
                                value: country.id.toString(),
                                label: country.name
                            })
                        )
                    }
                    users={
                        creatives_users.map(
                            (user) => ({
                                value: user.id.toString(),
                                label: user.name
                            })
                        )
                    }
                    tags={
                        creatives_tags.map(
                            (tag) => ({
                                value: tag.id.toString(),
                                label: tag.name
                            })
                        )
                    }
                    initialOpen={false}
                    initialFilters={filters.filter}
                    onFilterChange={handleFilterChange}
                />

                <div className="flex gap-4 justify-end items-center">
                    <PerPageDropdown
                        initialValue={filters.perPage}
                        onChange={handlePerPageChange}
                    />
                    <SortDropdown
                        initialSortType={filters.sort}
                        onSortChange={handleSortChange}
                    />
                </div>
            </div>

            {/* Сетка медиа-элементов */}
            <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                {!loadingFilters ? (
                    creatives.items.map((item, index) => (
                        <MediaCard
                            key={item.id}
                            id={item.id}
                            country={{name: item.country.name, flagUrl: item.country.img ?? ''}}
                            date={item.created_at}
                            tags={item.tags}
                            allTags={tags}
                            mediaType={item.type}
                            mediaSrc={item.url}
                            resolution={item.resolution ?? '1:1'}
                            code={item.code}
                            likeCount={item.likes_count}
                            dislikeCount={item.dislikes_count}
                            userLiked={item.user_liked}
                            userDisliked={item.user_disliked}
                            comments={item.comments.map(
                                (comment) => ({
                                    id: comment.id,
                                    author: {
                                        name: comment.user_name,
                                        avatarFallback: comment.user_name.slice(0,2).toUpperCase()
                                    },
                                    date: comment.created_at,
                                    content: comment.comment
                                })
                            )}
                            favorite={item.favorite}
                            statistic={item.statistic}

                            onSetFavorite={handleSetFavorite}
                            onPreviewOpen={() => handleOpenPreview(index)}
                            onAddComment={handleAddComment}
                            onLike={handleLike}
                            onDislike={handleDislike}
                            onOriginalDownload={(id) => handleDownload(id, "original")}
                            onUniqueDownload={(id) => handleDownload(id, "unique")}
                            thumbnailUrl={item.thumbnail}

                            onSave={handleSaveCreative}
                            onDelete={handleDeleteCreative}
                        />
                    ))
                ): (
                    Array.from({length: 16}).map((_, index) => (
                        <MediaCardSkeleton key={index} />
                    ))
                )
                }
            </div>

            <div className="flex justify-end my-4">
                <TablePagination
                    currentPage={creatives.currentPage}
                    totalPages={creatives.lastPage}
                    onPageChange={handlePageChange}
                    paginationItemsToDisplay={3}
                />
            </div>
        </AppLayout>
    );
}
