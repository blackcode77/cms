export interface SubnavLink { label: string, to: string }

export const subnav: SubnavLink[] = [
  { label: 'Мультимодальные перевозки', to: '/services/multimodal' },
  { label: 'Агентирование', to: '/services#agent' },
  { label: 'Проектные перевозки', to: '/project-cargo' },
  { label: 'Опасные грузы', to: '/dangerous-goods' },
]
