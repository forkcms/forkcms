<?php

namespace ForkCMS;

use Imagine\Exception\RuntimeException;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImagineController extends \Liip\ImagineBundle\Controller\ImagineController
{
    /**
     * This action applies a given filter to a given image, optionally saves the image and outputs it to the browser at the same time.
     *
     * @param Request $request
     * @param string  $path
     * @param string  $filter
     *
     * @throws \RuntimeException
     * @throws BadRequestHttpException
     *
     * @return RedirectResponse
     */
    public function filterAction(Request $request, $path, $filter)
    {
        // decoding special characters and whitespaces from path obtained from url
        $path = urldecode($path);
        $resolver = $request->get('resolver');

        try {
            if (!$this->cacheManager->isStored($path, $filter, $resolver)) {
                try {
                    $binary = $this->dataManager->find($filter, $path);
                } catch (NotLoadableException $e) {
                    if ($defaultImageUrl = $this->dataManager->getDefaultImageUrl($filter)) {
                        return new RedirectResponse($defaultImageUrl);
                    }

                    throw new NotFoundHttpException('Source image could not be found', $e);
                }

                if (in_array($binary->getMimeType(), ['image/svg', 'image/svg+xml'])) {
                    $this->cacheManager->store(
                        $binary,
                        $path,
                        $filter,
                        $resolver
                    );
                } else {
                    $this->cacheManager->store(
                        $this->filterManager->applyFilter($binary, $filter),
                        $path,
                        $filter,
                        $resolver
                    );
                }
            }

            return new RedirectResponse($this->cacheManager->resolve($path, $filter, $resolver), $this->redirectResponseCode);
        } catch (NonExistingFilterException $e) {
            $message = sprintf('Could not locate filter "%s" for path "%s". Message was "%s"', $filter, $path, $e->getMessage());

            if (null !== $this->logger) {
                $this->logger->debug($message);
            }

            throw new NotFoundHttpException($message, $e);
        } catch (RuntimeException $e) {
            throw new \RuntimeException(sprintf('Unable to create image for path "%s" and filter "%s". Message was "%s"', $path, $filter, $e->getMessage()), 0, $e);
        }
    }
}
