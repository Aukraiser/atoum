<?php

namespace mageekguy\atoum
{
	class emptyTest {}
	class notEmptyTest {}
}

namespace mageekguy\atoum\mock\mageekguy\atoum
{
	class test {}
}

namespace mageekguy\atoum\tests\units
{
	use
		mageekguy\atoum,
		mageekguy\atoum\mock
	;

	require_once __DIR__ . '/../runner.php';

	/**
	@ignore on
	@tags empty fake dummy
	@maxChildrenNumber 666
	*/
	class emptyTest extends atoum\test {}

	/**
	@ignore on
	*/
	class notEmptyTest extends atoum\test
	{
		/**
		@tags test method one method
		*/
		public function testMethod1() {}

		/**
		@ignore off
		@tags test method two
		*/
		public function testMethod2() {}

		public function aDataProvider()
		{
		}
	}

	class foo extends atoum\test
	{
		public function __construct()
		{
			$this->setTestedClassName('mageekguy\atoum\test');

			parent::__construct();
		}
	}

	class test extends atoum\test
	{
		public function testClassConstants()
		{
			$this
				->string(atoum\test::testMethodPrefix)->isEqualTo('test')
				->string(atoum\test::runStart)->isEqualTo('testRunStart')
				->string(atoum\test::beforeSetUp)->isEqualTo('beforeTestSetUp')
				->string(atoum\test::afterSetUp)->isEqualTo('afterTestSetUp')
				->string(atoum\test::beforeTestMethod)->isEqualTo('beforeTestMethod')
				->string(atoum\test::fail)->isEqualTo('testAssertionFail')
				->string(atoum\test::error)->isEqualTo('testError')
				->string(atoum\test::uncompleted)->isEqualTo('testUncompleted')
				->string(atoum\test::skipped)->isEqualTo('testSkipped')
				->string(atoum\test::exception)->isEqualTo('testException')
				->string(atoum\test::success)->isEqualTo('testAssertionSuccess')
				->string(atoum\test::afterTestMethod)->isEqualTo('afterTestMethod')
				->string(atoum\test::beforeTearDown)->isEqualTo('beforeTestTearDown')
				->string(atoum\test::afterTearDown)->isEqualTo('afterTestTearDown')
				->string(atoum\test::runStop)->isEqualTo('testRunStop')
				->string(atoum\test::defaultNamespace)->isEqualTo('#(?:^|\\\\)tests?\\\\units?\\\\#i')
			;
		}

		public function test__construct()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getScore())->isInstanceOf('mageekguy\atoum\score')
					->object($test->getLocale())->isEqualTo(new atoum\locale())
					->object($test->getAdapter())->isEqualTo(new atoum\adapter())
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->debugModeIsEnabled())->isFalse()
					->array($test->getAllTags())->isEqualTo($tags = array('empty', 'fake', 'dummy'))
					->array($test->getTags())->isEqualTo($tags)
					->array($test->getMethodTags())->isEmpty()
					->array($test->getDataProviders())->isEmpty()
					->integer($test->getMaxChildrenNumber())->isEqualTo(666)
					->boolean($test->codeCoverageIsEnabled())->isEqualTo(extension_loaded('xdebug'))
					->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
					->integer($test->getMaxChildrenNumber())->isEqualTo(666)
					->variable($test->getBootstrapFile())->isNull()
					->array($test->getClassPhpVersions())->isEmpty()
					->array($test->getMandatoryClassExtensions())->isEmpty()
					->array($test->getMandatoryMethodExtensions())->isEmpty()
			;
		}

		public function test__toString()
		{
			$this->castToString($this)->isEqualTo(__CLASS__);
		}

		public function test__get()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->assert)->isInstanceOf('mageekguy\atoum\test')
					->object($test->define)->isInstanceOf('mageekguy\atoum\test\asserter\generator')
					->object($test->mockGenerator)->isInstanceOf('mageekguy\atoum\mock\generator')
				->if($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))
				->then
					->object($test->mockGenerator)->isIdenticalTo($mockGenerator)
				->if($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator(new emptyTest())))
				->then
					->object($test->assert)->isIdenticalTo($test)
					->exception(function() use ($test, & $property) { $test->{$property = uniqid()}; })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Asserter \'' . $property . '\' does not exist')
			;
		}

		public function testEnableDebugMode()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->enableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isTrue()
					->object($test->enableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isTrue()
			;
		}

		public function testDisableDebugMode()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
				->if($test->enableDebugMode())
				->then
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
			;
		}

		public function testEnableCodeCoverage()
		{
			$this
				->assert('Code coverage must be enabled only if xdebug is available')
					->if($adapter = new atoum\test\adapter())
					->and($adapter->extension_loaded = function($extension) { return $extension == 'xdebug'; })
					->and($test = new emptyTest($adapter))
					->then
						->boolean($test->codeCoverageIsEnabled())->isTrue()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isTrue()
					->if($test->disableCodeCoverage())
					->then
						->boolean($test->codeCoverageIsEnabled())->isFalse()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isTrue()
				->assert('Code coverage must not be enabled if xdebug is not available')
					->if($adapter->extension_loaded = function($extension) { return $extension != 'xdebug'; })
					->and($test = new emptyTest($adapter))
					->then
						->boolean($test->codeCoverageIsEnabled())->isFalse()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testDisableCodeCoverage()
		{
			$this
				->if($adapter = new atoum\test\adapter())
				->and($adapter->extension_loaded = true)
				->and($test = new emptyTest($adapter))
				->then
					->boolean($test->codeCoverageIsEnabled())->isTrue()
					->object($test->disableCodeCoverage())->isIdenticalTo($test)
					->boolean($test->codeCoverageIsEnabled())->isFalse()
				->if($test->enableCodeCoverage())
				->then
					->boolean($test->codeCoverageIsEnabled())->isTrue()
					->object($test->disableCodeCoverage())->isIdenticalTo($test)
					->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testGetMockGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
				->if($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))
				->then
					->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
					->object($mockGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testSetMockGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))->isIdenticalTo($test)
					->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
					->object($mockGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testGetAsserterGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getAsserterGenerator())->isInstanceOf('mageekguy\atoum\test\asserter\generator')
				->if($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator($this)))
				->then
					->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
					->object($asserterGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testSetAsserterGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator($test)))->isIdenticalTo($test)
					->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
					->object($asserterGenerator->getTest())->isIdenticalTo($test)
					->object($asserterGenerator->getLocale())->isIdenticalTo($test->getLocale())
			;
		}

		public function testGetTestsSubNamespace()
		{
			$this
				->if($test = new self())
				->then
					->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->if($test->setTestNamespace($testsSubNamespace = uniqid()))
				->then
					->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
			;
		}

		public function testGetTestedClassName()
		{
			$mockClass = '\mock\\' . __CLASS__;

			$this
				->if($test = new $mockClass())
				->and($test->getMockController()->getClass = $testClass = 'foo')
				->then
					->exception(function() use ($test) { $test->getTestedClassName(); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Test class \'' . $testClass . '\' is not in a namespace which match pattern \'' . $test->getTestNamespace() . '\'')
				->if($test->getMockController()->getClass = 'tests\units\foo')
				->then
					->string($test->getTestedClassName())->isEqualTo('foo')
			;
		}

		public function testGetTestedClassPath()
		{
			$this
				->if($testedClass = new \reflectionClass($this->getTestedClassName()))
				->then
					->string($this->getTestedClassPath())->isEqualTo($testedClass->getFilename())
			;
		}

		public function testSetTestsSubNamespace()
		{
			$this
				->if($test = new self())
				->then
					->object($test->setTestNamespace($testsSubNamespace = uniqid()))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
					->object($test->setTestNamespace('\\' . ($testsSubNamespace = uniqid())))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
					->object($test->setTestNamespace('\\' . ($testsSubNamespace = uniqid()) . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
					->object($test->setTestNamespace(($testsSubNamespace = uniqid()) . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
					->object($test->setTestNamespace($testsSubNamespace = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo((string) $testsSubNamespace)
					->exception(function() use ($test) {
								$test->setTestNamespace('');
							}
						)
						->isInstanceOf('invalidArgumentException')
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test namespace must not be empty')
			;
		}

		public function testGetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			;
		}

		public function testSetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($test)
					->object($test->getAdapter())->isIdenticalTo($adapter)
			;
		}

		public function testSetLocale()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setLocale($locale = new atoum\locale()))->isIdenticalTo($test)
					->object($test->getLocale())->isIdenticalTo($locale)
			;
		}

		public function testSetScore()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setScore($score = new atoum\test\score()))->isIdenticalTo($test)
					->object($test->getScore())->isIdenticalTo($score)
			;
		}

		public function testSetBootstrapFile()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setBootstrapFile($path = uniqid()))->isIdenticalTo($test)
					->string($test->getBootstrapFile())->isEqualTo($path)
			;
		}

		public function testSetMaxChildrenNumber()
		{
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test) { $test->setMaxChildrenNumber(- rand(1, PHP_INT_MAX)); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Maximum number of children must be greater or equal to 1')
					->exception(function() use ($test) { $test->setMaxChildrenNumber(0); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Maximum number of children must be greater or equal to 1')
					->object($test->setMaxChildrenNumber($maxChildrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->integer($test->getMaxChildrenNumber())->isEqualTo($maxChildrenNumber)
					->object($test->setMaxChildrenNumber((string) $maxChildrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->integer($test->getMaxChildrenNumber())->isEqualTo($maxChildrenNumber)
			;
		}

		public function testGetClass()
		{
			$this
				->if($test = new emptyTest())
				->then
					->string($test->getClass())->isEqualTo(__NAMESPACE__ . '\emptyTest')
			;
		}

		public function testGetPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->string($test->getPath())->isEqualTo(__FILE__)
			;
		}

		public function testGetCoverage()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getCoverage())->isIdenticalTo($test->getScore()->getCoverage())
			;
		}

		public function testIgnore()
		{
			$this
				->if($test = new emptyTest())
				->then
					->boolean($test->isIgnored())->isTrue()
					->object($test->ignore(false))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
					->object($test->ignore(true))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
				->if($test = new notEmptyTest())
				->then
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
					->object($test->ignore(false))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isFalse()
					->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->object($test->ignore(true))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->methodIsIgnored('testMethod1'))->istrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
			;
		}

		public function testGetCurrentMethod()
		{
			$this
				->if($test = new emptyTest())
				->then
					->variable($test->getCurrentMethod())->isNull()
			;
		}

		public function testCount()
		{
			$this
				->sizeOf(new emptyTest())->isEqualTo(0)
				->if($test = new notEmptyTest())
				->then
					->sizeOf($test)->isEqualTo(0)
				->if($test->ignore(false))
				->then
					->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->sizeOf($test)->isEqualTo(2)
				->if($test->ignoreMethod('testMethod1', true))
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->sizeOf($test)->isEqualTo(1)
				->if($test->ignoreMethod('testMethod2', true))
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
					->sizeOf($test)->isEqualTo(0)
			;
		}

		public function testGetTestMethods()
		{
			$this
				->if($test = new emptyTest())
				->then
					->boolean($test->ignore(false)->isIgnored())->isTrue()
					->sizeOf($test)->isZero()
					->array($test->getTestMethods())->isEmpty()
				->if($test = new notEmptyTest())
				->then
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
					->sizeOf($test)->isEqualTo(0)
					->array($test->getTestMethods())->isEmpty()
					->boolean($test->ignore(false)->isIgnored())->isFalse()
					->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->sizeOf($test)->isEqualTo(2)
					->array($test->getTestMethods())->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('method')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('two')))->isEqualTo(array('testMethod2'))
					->array($test->getTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTestMethods(array('test', 'method')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test', 'method', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test', 'method', 'two', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
			;
		}

		public function testGetPhpPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->variable($test->getPhpPath())->isNull()
				->if($test->setPhpPath($phpPath = uniqid()))
				->then
					->string($test->getPhpPath())->isEqualTo($phpPath)
			;
		}

		public function testSetPhpPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setPhpPath($phpPath = uniqid()))->isIdenticalTo($test)
					->string($test->getPhpPath())->isIdenticalTo($phpPath)
					->object($test->setPhpPath($phpPath = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->string($test->getPhpPath())->isIdenticalTo((string) $phpPath)
			;
		}

		public function testMethodIsIgnored()
		{
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->methodIsIgnored($method = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
			;
		}

		public function testSetTags()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setTags($tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
					->array($test->getTags())->isEqualTo($tags)
			;
		}

		public function testSetMethodTags()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->setMethodTags('testMethod1', $tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
					->array($test->getMethodTags('testMethod1'))->isEqualTo($tags)
					->exception(function() use ($test, & $method) { $test->setMethodTags($method = uniqid(), array()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
			;
		}

		public function testGetMethodTags()
		{
			$this
				->if($test = new notemptyTest())
				->then
					->array($test->getMethodTags('testMethod1'))->isEqualTo(array('test', 'method', 'one'))
					->exception(function() use ($test, & $method) { $test->getMethodTags($method = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
			;
		}

		public function testAddMandatoryClassExtension()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->addMandatoryClassExtension($extension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryClassExtensions())->isEqualTo(array($extension))
					->object($test->addMandatoryClassExtension($otherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryClassExtensions())->isEqualTo(array($extension, $otherExtension))
			;
		}

		public function testAddMandatoryMethodExtension()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->addMandatoryMethodExtension($method = uniqid(), uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
					->object($test->addMandatoryMethodExtension('testMethod1', $extension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension), 'testMethod2' => array()))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEmpty()
					->object($test->addMandatoryMethodExtension('testMethod1', $otherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension, $otherExtension), 'testMethod2' => array()))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEmpty()
					->object($test->addMandatoryMethodExtension('testMethod2', $anOtherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension, $otherExtension), 'testMethod2' => array($anOtherExtension)))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array($anOtherExtension))
				->if($test->addMandatoryClassExtension($classExtension = uniqid()))
				->then
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($classExtension, $extension, $otherExtension), 'testMethod2' => array($classExtension, $anOtherExtension)))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($classExtension, $extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array($classExtension, $anOtherExtension))
			;
		}

		public function testAddClassPhpVersion()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->addClassPhpVersion('5.3'))->isIdenticalTo($test)
					->array($test->getClassPhpVersions())->isEqualTo(array('5.3' => '>='))
					->object($test->addClassPhpVersion('5.4', '<='))->isIdenticalTo($test)
					->array($test->getClassPhpVersions())->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
			;
		}

		public function testAddMethodPhpVersion()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->addMethodPhpVersion($method, '6.0'); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
					->object($test->addMethodPhpVersion('testMethod1', '5.3'))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>='), 'testMethod2' => array()))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEmpty()
					->object($test->addMethodPhpVersion('testMethod1', '5.4', '<='))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>=', '5.4' => '<='), 'testMethod2' => array()))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEmpty()
					->object($test->addMethodPhpVersion('testMethod2', '5.4', '>='))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>=', '5.4' => '<='), 'testMethod2' => array('5.4' => '>=')))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEqualTo(array('5.4' => '>='))
				->if($test->addClassPhpVersion('5.5'))
				->then
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.5' => '>=', '5.3' => '>=', '5.4' => '<='), 'testMethod2' => array('5.5' => '>=', '5.4' => '>=')))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.5' => '>=', '5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEqualTo(array('5.5' => '>=', '5.4' => '>='))
			;
		}

		public function testRun()
		{
			$this
				->mockTestedClass('mock\tests\units')
				->if($test = new \mock\tests\units\test())
				->then
					->object($test->run())->isIdenticalTo($test)
					->mock($test)
						->call('callObservers')
							->withArguments(\mageekguy\atoum\test::runStart)->never()
							->withArguments(\mageekguy\atoum\test::runStop)->never()
							->withArguments(\mageekguy\atoum\test::beforeSetUp)->never()
							->withArguments(\mageekguy\atoum\test::afterSetUp)->never()
							->withArguments(\mageekguy\atoum\test::beforeTestMethod)->never()
							->withArguments(\mageekguy\atoum\test::afterTestMethod)->never()
			;
		}

		public function testSetTestedClassName()
		{
			$this
				->if($test = new foo())
				->then
					->string($test->getTestedClassName())->isEqualTo('mageekguy\atoum\test')
					->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Tested class name is already defined')
				->if($test = new self())
				->then
					->object($test->setTestedClassName($class = uniqid()))->isIdenticalTo($test)
					->string($test->getTestedClassName())->isEqualTo($class)
					->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Tested class name is already defined')
			;
		}

		public function testMockClass()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->mockClass(__CLASS__))->isIdenticalTo($test)
					->class('mock\\' . __CLASS__)->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo'))->isIdenticalTo($test)
					->class('foo\test')->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo\bar'))->isIdenticalTo($test)
					->class('foo\bar\test')->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo', 'bar'))->isIdenticalTo($test)
					->class('foo\bar')->isSubClassOf(__CLASS__)
			;
		}

		public function testMockTestedClass()
		{
			$this
				->if($test = new emptyTest())
				->and($testedClassName = $test->getTestedClassName())
				->then
					->object($test->mockTestedClass())->isIdenticalTo($test)
					->class('mock\\' . $testedClassName)->isSubClassOf($testedClassName)
					->object($test->mockTestedClass('foo'))->isIdenticalTo($test)
					->class('foo\emptyTest')->isSubClassOf($testedClassName)
					->object($test->mockTestedClass('foo\bar'))->isIdenticalTo($test)
					->class('foo\bar\emptyTest')->isSubClassOf($testedClassName)
					->object($test->mockTestedClass('foo', 'bar'))->isIdenticalTo($test)
					->class('foo\bar')->isSubClassOf($testedClassName)
			;
		}

		public function testGetTaggedTestMethods()
		{
			$this
				->if($test = new emptyTest())
				->then
					->array($test->getTaggedTestMethods(array()))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), uniqid())))->isEmpty()
				->if($test = new notEmptyTest())
				->then
					->array($test->getTaggedTestMethods(array()))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid(), 'testMethod2')))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEmpty()
				->if($test->ignore(false))
				->then
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEqualTo(array('testMethod1'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod2', uniqid())))->isEqualTo(array('testMethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEqualTo(array('Testmethod1', 'Testmethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2'), array('one')))->isEqualTo(array('Testmethod1'))
				->if($test->ignoreMethod('testMethod1', true))
				->then
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod2', uniqid())))->isEqualTo(array('testMethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEqualTo(array('Testmethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2'), array('one')))->isEmpty()
			;
		}

		public function testSetDataProvider()
		{
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->setDataProvider($method = uniqid(), uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $dataProvider) { $test->setDataProvider('testMethod1', $dataProvider = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Data provider ' . get_class($test) . '::' . $dataProvider . '() is unknown')
					->object($test->setDataProvider('testMethod1', 'aDataProvider'))->isIdenticalTo($test)
					->array($test->getDataProviders())->isEqualTo(array('testMethod1' => 'aDataProvider'))
			;
		}

		public function testCalling()
		{
			$this
				->if($test = new emptyTest())
				->and($mock = new \mock\foo())
				->and($test->calling($mock)->bar = $value = uniqid())
				->then
					->string($mock->bar())->isEqualTo($value)
				->and($test->ƒ($mock)->bar = $otherValue = uniqid())
				->then
					->string($mock->bar())->isEqualTo($otherValue)
			;
		}

		public function testResetMock()
		{
			$this
				->if($test = new emptyTest())
				->and($mock = new \mock\foo())
				->then
					->object($test->resetMock($mock))->isIdenticalTo($mock->getMockController())
					->array($mock->getMockController()->getCalls())->isEmpty()
				->if($mock->bar())
				->then
					->object($test->resetMock($mock))->isIdenticalTo($mock->getMockController())
					->array($mock->getMockController()->getCalls())->isEmpty()
			;
		}

		public function testResetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->and($adapter = new atoum\test\adapter())
				->then
					->object($test->resetAdapter($adapter))->isIdenticalTo($adapter)
					->array($adapter->getCalls())->isEmpty()
				->if($adapter->md5(uniqid()))
				->then
					->object($test->resetAdapter($adapter))->isIdenticalTo($adapter)
					->array($adapter->getCalls())->isEmpty()
			;
		}
	}
}
