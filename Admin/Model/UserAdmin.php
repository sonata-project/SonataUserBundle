<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\Admin\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || is_null($this->getSubject()->getId())) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(parent::getExportFields(), function ($v) {
            return !in_array($v, array('password', 'salt'));
        });
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createdAt')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('locked')
            ->add('email')
            ->add('groups')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
            ->with('Groups')
                ->add('groups')
            ->end()
            ->with('Profile')
                ->add('dateOfBirth')
                ->add('firstname')
                ->add('lastname')
                ->add('website')
                ->add('biography')
                ->add('gender')
                ->add('locale')
                ->add('timezone')
                ->add('phone')
            ->end()
            ->with('Social')
                ->add('facebookUid')
                ->add('facebookName')
                ->add('twitterUid')
                ->add('twitterName')
                ->add('gplusUid')
                ->add('gplusName')
            ->end()
            ->with('Security')
                ->add('token')
                ->add('twoStepVerificationCode')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('User')
                ->with('Profile', array('class' => 'col-md-6'))->end()
                ->with('General', array('class' => 'col-md-6'))->end()
                ->with('Social', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
                ->with('Keys', array('class' => 'col-md-4'))->end()
                ->with('Roles', array('class' => 'col-md-12'))->end()
            ->end()
        ;

        $now = new \DateTime();

        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $textType = 'Symfony\Component\Form\Extension\Core\Type\TextType';
            $datePickerType = 'Sonata\CoreBundle\Form\Type\DatePickerType';
            $urlType = 'Symfony\Component\Form\Extension\Core\Type\UrlType';
            $userGenderType = 'Sonata\UserBundle\Form\Type\UserGenderListType';
            $localeType = 'Symfony\Component\Form\Extension\Core\Type\LocaleType';
            $timezoneType = 'Symfony\Component\Form\Extension\Core\Type\TimezoneType';
            $modelType = 'Sonata\AdminBundle\Form\Type\ModelType';
            $securityRolesType = 'Sonata\UserBundle\Form\Type\SecurityRolesType';
        } else {
            $textType = 'text';
            $datePickerType = 'sonata_type_date_picker';
            $urlType = 'url';
            $userGenderType = 'sonata_user_gender';
            $localeType = 'locale';
            $timezoneType = 'timezone';
            $modelType = 'sonata_type_model';
            $securityRolesType = 'sonata_security_roles';
        }

        $formMapper
            ->tab('User')
                ->with('General')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', $textType, array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ))
                ->end()
                ->with('Profile')
                    ->add('dateOfBirth', $datePickerType, array(
                        'years' => range(1900, $now->format('Y')),
                        'dp_min_date' => '1-1-1900',
                        'dp_max_date' => $now->format('c'),
                        'required' => false,
                    ))
                    ->add('firstname', null, array('required' => false))
                    ->add('lastname', null, array('required' => false))
                    ->add('website', $urlType, array('required' => false))
                    ->add('biography', $textType, array('required' => false))
                    ->add('gender', $userGenderType, array(
                        'required' => true,
                        'translation_domain' => $this->getTranslationDomain(),
                    ))
                    ->add('locale', $localeType, array('required' => false))
                    ->add('timezone', $timezoneType, array('required' => false))
                    ->add('phone', null, array('required' => false))
                ->end()
                ->with('Social')
                    ->add('facebookUid', null, array('required' => false))
                    ->add('facebookName', null, array('required' => false))
                    ->add('twitterUid', null, array('required' => false))
                    ->add('twitterName', null, array('required' => false))
                    ->add('gplusUid', null, array('required' => false))
                    ->add('gplusName', null, array('required' => false))
                ->end()
            ->end()
            ->tab('Security')
                ->with('Status')
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                    ->add('groups', $modelType, array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                ->end()
                ->with('Roles')
                    ->add('realRoles', $securityRolesType, array(
                        'label' => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ))
                ->end()
                ->with('Keys')
                    ->add('token', null, array('required' => false))
                    ->add('twoStepVerificationCode', null, array('required' => false))
                ->end()
            ->end()
        ;
    }
    
    /**
     * @param UserInterface $object
     * @return UserInterface
     */
    public function update($object)
    {
        $this->preUpdate($object);
        foreach ($this->extensions as $extension) {
            $extension->preUpdate($this, $object);
        }
        
        $originalData = $this->getConfigurationPool()
            ->getContainer()->get('doctrine')->getEntityManager()
            ->getUnitOfWork()->getOriginalEntityData($object)
        ;
        
        $this->getUserManager()->updateUser($object);
        if ($originalData['username'] !== $object->getUserName()) {
            /* Symfony\Component\Security\Acl\Dbal\MutableAclProvider */
            $this->getConfigurationPool()->getContainer()
                ->get('security.acl.provider')->updateUserSecurityIdentity(
                        UserSecurityIdentity::fromAccount($object),
                        $originalData['username']
                )
            ;
        }
        
        $this->postUpdate($object);
        foreach ($this->extensions as $extension) {
            $extension->postUpdate($this, $object);
        }
        
        return $object;
    }
    
    /**
     * @param UserInterface $object
     */
    public function delete($object)
    {
        $this->preRemove($object);
        foreach ($this->extensions as $extension) {
            $extension->preRemove($this, $object);
        }
        
        $this->getSecurityHandler()->deleteObjectSecurity($this, $object);
        $this->getUserManager()->delete($object);
        
        /* Symfony\Component\Security\Acl\Dbal\MutableAclProvider */
        $this->getConfigurationPool()->getContainer()
            ->get('security.acl.provider')->deleteSecurityIdentity(
                UserSecurityIdentity::fromAccount($object)
            )
        ;
        
        $this->postRemove($object);
        foreach ($this->extensions as $extension) {
            $extension->postRemove($this, $object);
        }
    }
    
    /**
     * remove access to batchDelete, because
     *  Sonata\AdminBundle\Controller\CrudController::batchActionDelete()
     *  uses $modelManager = $this->admin->getModelManager()
     *  and ModelManager dont delete securityIdentity
     * @return array
     */
    protected function getAccess()
    {
        $access = parent::getAccess();
        unset($access['batchDelete']);
        return $access;
    }
    
    /**
     * remove batchDelete action, because
     *  Sonata\AdminBundle\Controller\CrudController::batchActionDelete()
     *  uses $modelManager = $this->admin->getModelManager()
     *  and ModelManager dont delete securityIdentity
     * @return array
     */
    public function getBatchActions() {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }
    
}
