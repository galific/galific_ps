<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerSiy39ve\appProdProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerSiy39ve/appProdProjectContainer.php') {
    touch(__DIR__.'/ContainerSiy39ve.legacy');

    return;
}

if (!\class_exists(appProdProjectContainer::class, false)) {
    \class_alias(\ContainerSiy39ve\appProdProjectContainer::class, appProdProjectContainer::class, false);
}

return new \ContainerSiy39ve\appProdProjectContainer(array(
    'container.build_hash' => 'Siy39ve',
    'container.build_id' => 'e340e27f',
    'container.build_time' => 1550429348,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerSiy39ve');
