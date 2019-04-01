<?php








	/*#################################################################################################################################################*/
	
	
	
	

	/**********************************************************************
	*  Author: Justin Vincent (jv@vip.ie)
           * Author: Stefanie Janine Stoelting <mail@stefanie-stoelting.de>
           * Contributor:  Lawrence Stubbs <technoexpressnet@gmail.com>
	*  Web...: http://justinvincent.com
	*  Name..: ezSQL
	*  Desc..: ezSQL Core module - database abstraction library to make
	*          it very easy to deal with databases. ezSQLcore can not be used by
	*          itself (it is designed for use by database specific modules).
           *
	*/

	/**********************************************************************
	*  ezSQL Constants
	*/

	defined('EZSQL_VERSION') or define('EZSQL_VERSION', '3.08');
	defined('OBJECT') or define('OBJECT', 'OBJECT');
	defined('ARRAY_A') or define('ARRAY_A', 'ARRAY_A');
	defined('ARRAY_N') or define('ARRAY_N', 'ARRAY_N');

	/**********************************************************************
	*  Core class containing common functions to manipulate query result
	*  sets once returned
	*/

    //require_once('ezFunctions.php');
    
/**
 * Author:  Lawrence Stubbs <technoexpressnet@gmail.com>
 *
 * Important: Verify that every feature you use will work with your database vendor.
 * ezSQL Query Builder will attempt to validate the generated SQL according to standards.
 * Any errors will return an boolean false, and you will be responsible for handling.
 *
 * ezQuery does no validation whatsoever if certain features even work with the
 * underlying database vendor. 
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */
 
	// ezQuery prepare placeholder/positional tag
		const _TAG = '__ez__';
    // Use to set get_result output as json 
        const _JSON = 'json';
 
    /*
     * Operator boolean expressions.
     */
		const EQ  = '=';
		const NEQ = '<>';
		const NE  = '!=';
		const LT  = '<';
		const LTE = '<=';
		const GT  = '>';
		const GTE = '>=';
    
		const _IN = 'IN';
		const _notIN = 'NOT IN';
		const _LIKE = 'LIKE';
		const _notLIKE  = 'NOT LIKE';
		const _BETWEEN = 'BETWEEN';
		const _notBETWEEN = 'NOT BETWEEN';
        
		const _isNULL = 'IS NULL';
		const _notNULL  = 'IS NOT NULL';
    
    /*
     * Combine operators .
     */    
		const _AND = 'AND';
		const _OR = 'OR';
		const _NOT = 'NOT';
		const _andNOT = 'AND NOT'; 
                
        // Global class instances, will be used to create and call methods directly.
        $_ezQuery = null;
       // $_ezCubrid = null;
        $_ezMysqli = null;
       // $_ezOracle8_9 = null;
       // $_ezOracleTNS = null;
        $_ezPdo = null;
        $_ezPostgresql = null;
        $_ezRecordset = null;
        $_ezSqlite3 = null;
        $_ezSqlsrv = null;
 
	/**********************************************************************
     * Creates an array from expressions in the following formate
     * param:  strings @x,        The left expression.
     *                 @operator, One of '<', '>', '=', '!=', '>=', '<=', '<>', 'IN',, 'NOT IN', 'LIKE', 
     *                              'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'IS', 'IS NOT', or  the constants above.
     *                 @y,        The right expression.
     *                 @and,        combine additional expressions with,  'AND','OR', 'NOT', 'AND NOT'.
     *                 @args          for any extras
     *
     * function comparison($x, $operator, $y, $and=null, ...$args)
     *  {
     *          return array($x, $operator, $y, $and, ...$args);
     * }    
     * @returns: array
     ***********************************************************************/
    
    /**
     * Creates an equality comparison expression with the given arguments.
     */
    function eq($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, EQ, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a non equality comparison expression with the given arguments.
     */
    function neq($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, NEQ, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates the other non equality comparison expression with the given arguments.
     */
    function ne($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, NE, $y, $and, ...$args);
        return $expression;
    }
    
    /**
     * Creates a lower-than comparison expression with the given arguments.
     */
    function lt($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, LT, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a lower-than-equal comparison expression with the given arguments.
     */
    function lte($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, LTE, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a greater-than comparison expression with the given arguments.
     */
    function gt($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, GT, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a greater-than-equal comparison expression with the given arguments.
     */
    function gte($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, GTE, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates an IS NULL expression with the given arguments.
     */
    function isNull($x, $y='null', $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _isNULL, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     */
    function isNotNull($x, $y='null', $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _notNULL, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a LIKE() comparison expression with the given arguments.
     */
    function like($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _LIKE, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a NOT LIKE() comparison expression with the given arguments.
     */
    function notLike($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _notLIKE, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a IN () comparison expression with the given arguments.
     */
    function in($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _IN, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a NOT IN () comparison expression with the given arguments.
     */
    function notIn($x, $y, $and=null, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _notIN, $y, $and, ...$args);
        return $expression;
    }

    /**
     * Creates a BETWEEN () comparison expression with the given arguments.
     */
    function between($x, $y, $y2, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _BETWEEN,$y, $y2, ...$args);
        return $expression;
    }

    /**
     * Creates a NOT BETWEEN () comparison expression with the given arguments.
     */
    function notBetween($x, $y, $y2, ...$args)
    {
        $expression = array();
        array_push($expression, $x, _notBETWEEN, $y, $y2, ...$args);
        return $expression;
    }
    
    /**
       * desc: Using global class instances, setup functions to call class methods directly.
       * param: @ezSQL - string, representing class  'cubrid', 'mysqli', 'oracle8_9', 'oracletns', 'pdo', 'postgresql', 'recordset', 'sqlite3', 'sqlsrv'
       * returns: boolean - true, or false for error
       */
    function setQuery($ezSQL='') {
        global $_ezQuery, $_ezMysqli;// $_ezCubrid, $_ezOracle8_9, $_ezOracleTNS; 'recordset' ,'oracle8_9', 'oracletns',
        global $_ezPdo, $_ezPostgresql, $_ezRecordset, $_ezSqlite3, $_ezSqlsrv;
        if (in_array(strtolower($ezSQL), array( 'cubrid', 'mysqli', 'pdo', 'postgresql', 'sqlite3', 'sqlsrv' ))) {
            switch(strtolower($ezSQL)) {
            //    case 'cubrid':
            //        $_ezQuery = $_ezCubrid;
            //        break;
                case 'mysqli':
                    $_ezQuery = $_ezMysqli;
                    break;
            //    case 'oracle8_9':
            //        $_ezQuery = $_ezOracle8_9;
            //        break;
            //    case 'oracletns':
            //        $_ezQuery = $_ezOracleTNS;
            //        break;
                case 'pdo':
                    $_ezQuery = $_ezPdo;
                    break;
                case 'postgresql':
                    $_ezQuery = $_ezPostgresql;
                    break;
                case 'recordset':
                    $_ezQuery = $_ezRecordset;
                    break;
                case 'sqlite3':
                    $_ezQuery = $_ezSqlite3;
                    break;
                case 'sqlsrv':
                    $_ezQuery = $_ezSqlsrv;
                    break;                    
            }
            return (!empty($_ezQuery)) ? true: false;            
        } else {
			$_ezQuery = null;
            unset($_ezQuery);
            return false;            
        }
    }     
    
    function select($table='', $columns='*', ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->selecting($table, $columns, ...$args) : false;
    } 
    
    function select_into($newtable, $fromcolumns='*', $oldtable=null, ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->select_into($newtable, $fromcolumns, $oldtable, ...$args) : false;
    } 
    
    function insert_select($totable='', $tocolumns='*', $fromtable, $fromcolumns='*', ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->insert_select($totable, $tocolumns, $fromtable, $fromcolumns, ...$args) : false;
    }     
    
    function create_select($newtable, $fromcolumns, $oldtable=null, ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->create_select($newtable, $fromcolumns, $oldtable, ...$args) : false;
    }  
    
    function where( ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->where( ...$args) : false;
    } 
    
    function groupBy($groupBy) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->groupBy($groupBy) : false;
    } 
    
    function having( ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->having( ...$args) : false;
    }
    
    function orderBy($orderBy, $order) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->orderBy($orderBy, $order) : false;
    } 
    
    function insert($table='', $keyvalue) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->insert($table, $keyvalue) : false;
    } 
    
    function update($table='', $keyvalue, ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->update($table, $keyvalue, ...$args) : false;
    } 
    
    function delete($table='', ...$args) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->delete($table, ...$args) : false;
    } 
        
    function replace($table='', $keyvalue) {
        global $_ezQuery;
        return ($_ezQuery) ? $_ezQuery->replace($table, $keyvalue) : false;
    }      
    
    
    
    
    
    
    
    
    //require_once('ezQuery.php');
    
/**
 * Author:  Lawrence Stubbs <technoexpressnet@gmail.com>
 *
 * Important: Verify that every feature you use will work with your database vendor.
 * ezSQL Query Builder will attempt to validate the generated SQL according to standards.
 * Any errors will return an boolean false, and you will be responsible for handling.
 *
 * ezQuery does no validation whatsoever if certain features even work with the
 * underlying database vendor. 
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

class ezQuery
{ 		
	protected $select_result = true;
	protected $prepareActive = false;
    
	private $fromtable = null;
    private $iswhere = true;    
    private $isinto = false;
    
    function __construct()
		{
		}
        
    function clean($string) 
    {
        $patterns = array( // strip out:
                '@<script[^>]*?>.*?</script>@si', // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',          // HTML tags
                '@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'       // Strip multi-line comments
                );
                
        $string = preg_replace($patterns,'',$string);
        $string = trim($string);
        $string = stripslashes($string);
        
        return htmlentities($string);
    }
    
    // return status of prepare function availability in method calls
    function getPrepare($on=true) {
        return $this->prepareActive;
	}
  	
    // turn off/on prepare function availability in ezQuery method calls 
    function setPrepare($on=true) {
        $this->prepareActive = ($on) ? true : false;
		return null;
	}  	
    
    // returns array of parameter values for prepare function 
    function getParamaters() {
		return $this->preparedvalues;
	}
    
    /**
        * desc: add parameter values to class array variable for prepare function or clear if no value supplied
        * param: @valuetoadd mixed
        *
        * returns int - array count
        */
    function setParamaters($valuetoadd=null) {
        if (empty($valuetoadd)) {
            $this->preparedvalues = array();
            return null;
        } else 
            return array_push($this->preparedvalues, $valuetoadd); 
	}
    
    function to_string($arrays) {        
        if (is_array( $arrays )) {
            $columns = '';
            foreach($arrays as $val) {
                $columns .= $val.', ';
            }
            $columns = rtrim($columns, ', ');            
        } else
            $columns = $arrays;
        return $columns;
    }
            
    /**
    * desc: specifies a grouping over the results of the query.
    * <code>
    *     $this->selecting('table', 
    *                   columns,
    *                   where(columns  =  values),
    *                   groupBy(columns),
    *                   having(columns  =  values),
    *                   orderBy(order);
    * </code>
    * param: mixed @groupBy The grouping expression.  
	*
    * returns: string - GROUP BY SQL statement, or false on error
    */
    function groupBy($groupBy)
    {
        if (empty($groupBy)) {
            return false;
        }
        
        $columns = $this->to_string($groupBy);
        
        return 'GROUP BY ' .$columns;
    }

    /**
    * desc: specifies a restriction over the groups of the query. 
	* formate: having( array(x, =, y, and, extra) ) or having( "x  =  y  and  extra" );
	* example: having( array(key, operator, value, combine, extra) ); or having( "key operator value combine extra" );
    * param: mixed @array or @string double spaced "(key, - table column  
    *        	operator, - set the operator condition, either '<','>', '=', '!=', '>=', '<=', '<>', 'in', 'like', 'between', 'not between', 'is null', 'is not null'
	*		value, - will be escaped
    *        	combine, - combine additional where clauses with, either 'AND','OR', 'NOT', 'AND NOT' or  carry over of @value in the case the @operator is 'between' or 'not between'
	*		extra - carry over of @combine in the case the operator is 'between' or 'not between')"
    * @returns: string - HAVING SQL statement, or false on error
    */
    function having(...$having)
    {
        $this->iswhere = false;
        return $this->where( ...$having);
    }
 
    /**
    * desc: specifies an ordering for the query results.  
    * param:  @order The ordering direction. 
    * returns: string - ORDER BY SQL statement, or false on error
    */
    function orderBy($orderBy, $order)
    {
        if (empty($orderBy)) {
            return false;
        }
        
        $columns = $this->to_string($orderBy);
        
        $order = (in_array(strtoupper($order), array( 'ASC', 'DESC'))) ? strtoupper($order) : 'ASC';
        
        return 'ORDER BY '.$columns.' '. $order;
    }
   
 	/**********************************************************************
         * desc: helper returns an WHERE sql clause string 
	* formate: where( array(x, =, y, and, extra) ) or where( "x  =  y  and  extra" );
	* example: where( array(key, operator, value, combine, extra) ); or where( "key operator value combine extra" );
	* param: mixed @array or @string double spaced "(key, - table column  
         *        	operator, - set the operator condition, either '<','>', '=', '!=', '>=', '<=', '<>', 'in', 'like', 'not like', 'between', 'not between', 'is null', 'is not null'
	*		value, - will be escaped
         *        	combine, - combine additional where clauses with, either 'AND','OR', 'NOT', 'AND NOT' or  carry over of @value in the case the @operator is 'between' or 'not between'
	*		extra - carry over of @combine in the case the operator is 'between' or 'not between')"
         * returns: string - WHERE SQL statement, or false on error
	*/        
    function where( ...$getwherekeys) {      
        $whereorhaving = ($this->iswhere) ? 'WHERE' : 'HAVING';
        $this->iswhere = true;
        
		if (!empty($getwherekeys)){
			if (is_string($getwherekeys[0])) {
				foreach ($getwherekeys as $makearray) 
					$wherekeys[] = explode('  ',$makearray);	
			} else 
				$wherekeys = $getwherekeys;			
		} else 
			return '';
		
		foreach ($wherekeys as $values) {
			$operator[] = (isset($values[1])) ? $values[1]: '';
			if (!empty($values[1])){
				if (strtoupper($values[1]) == 'IN') {
					$wherekey[ $values[0] ] = array_slice($values,2);
					$combiner[] = (isset($values[3])) ? $values[3]: _AND;
					$extra[] = (isset($values[4])) ? $values[4]: null;				
				} else {
					$wherekey[ (isset($values[0])) ? $values[0] : '1' ] = (isset($values[2])) ? $values[2] : '' ;
					$combiner[] = (isset($values[3])) ? $values[3]: _AND;
					$extra[] = (isset($values[4])) ? $values[4]: null;
				}				
			} else {
                $this->setParamaters();
				return false;
            }                
		}
        
        $where='1';    
        if (! isset($wherekey['1'])) {
            $where='';
            $i=0;
            $needtoskip=false;
            foreach($wherekey as $key=>$val) {
                $iscondition = strtoupper($operator[$i]);
				$combine = $combiner[$i];
				if ( in_array(strtoupper($combine), array( 'AND', 'OR', 'NOT', 'AND NOT' )) || isset($extra[$i])) 
					$combinewith = (isset($extra[$i])) ? $combine : strtoupper($combine);
				else 
					$combinewith = _AND;
                if (! in_array( $iscondition, array( '<', '>', '=', '!=', '>=', '<=', '<>', 'IN', 'LIKE', 'NOT LIKE', 'BETWEEN', 'NOT BETWEEN', 'IS', 'IS NOT' ) )) {
                    $this->setParamaters();
                    return false;
                } else {
                    if (($iscondition=='BETWEEN') || ($iscondition=='NOT BETWEEN')) {
						$value = $this->escape($combinewith);
						if (in_array(strtoupper($extra[$i]), array( 'AND', 'OR', 'NOT', 'AND NOT' ))) 
							$mycombinewith = strtoupper($extra[$i]);
						else 
                            $mycombinewith = _AND;
						if ($this->getPrepare()) {
							$where.= "$key ".$iscondition.' '._TAG." AND "._TAG." $mycombinewith ";
							$this->setParamaters($val);
							$this->setParamaters($combinewith);
						} else 
							$where.= "$key ".$iscondition." '".$this->escape($val)."' AND '".$value."' $mycombinewith ";
						$combinewith = $mycombinewith;
					} elseif ($iscondition=='IN') {
						$value = '';
						foreach ($val as $invalues) {
							if ($this->getPrepare()) {
								$value .= _TAG.', ';
								$this->setParamaters($invalues);
							} else 
								$value .= "'".$this->escape($invalues)."', ";
						}													
						$where.= "$key ".$iscondition." ( ".rtrim($value, ', ')." ) $combinewith ";
					} elseif(((strtolower($val)=='null') || ($iscondition=='IS') || ($iscondition=='IS NOT'))) {
                        $iscondition = (($iscondition=='IS') || ($iscondition=='IS NOT')) ? $iscondition : 'IS';
                        $where.= "$key ".$iscondition." NULL $combinewith ";
                    } elseif((($iscondition=='LIKE') || ($iscondition=='NOT LIKE')) && ! preg_match('/[_%?]/',$val)) return false;
                    else {
						if ($this->getPrepare()) {
							$where.= "$key ".$iscondition.' '._TAG." $combinewith ";
							$this->setParamaters($val);
						} else 
							$where.= "$key ".$iscondition." '".$this->escape($val)."' $combinewith ";
					}
                    $i++;
                }
            }
            $where = rtrim($where, " $combinewith ");
        }
		
        if (($this->getPrepare()) && !empty($this->getParamaters()) && ($where!='1'))
			return " $whereorhaving ".$where.' ';
		else
			return ($where!='1') ? " $whereorhaving ".$where.' ' : ' ' ;
    }        
    
	/**********************************************************************
    * desc: returns an sql string or result set given the table, fields, by operator condition or conditional array
    *<code>
    *selecting('table', 
    *        'columns',
    *        where( eq( 'columns', values, _AND ), like( 'columns', _d ) ),
    *        groupBy( 'columns' ),
    *        having( between( 'columns', values1, values2 ) ),
    *        orderBy( 'columns', 'desc' );
    *</code>    
    *
    * param: @table, - database table to access
    *        @fields, - table columns, string or array
    *        @wherekey, - where clause ( array(x, =, y, and, extra) ) or ( "x  =  y  and  extra" )
    *        @groupby, - 
    *        @having, - having clause ( array(x, =, y, and, extra) ) or ( "x  =  y  and  extra" )
    *        @orderby - 	*   
    * returns: a result set - see docs for more details, or false for error
	*/
    function selecting($table='', $fields='*', ...$get_args) {    
		$getfromtable = $this->fromtable;
		$getselect_result = $this->select_result;       
		$getisinto = $this->isinto;
        
		$this->fromtable = null;
		$this->select_result = true;	
		$this->isinto = false;	
        
        $skipwhere = false;
        $wherekeys = $get_args;
        $where = '';
		
        if ( ! isset($table) || $table=='' ) {
            $this->setParamaters();
            return false;
        }
        
        $columns = $this->to_string($fields);
        
		if (isset($getfromtable) && ! $getisinto) 
			$sql="CREATE TABLE $table AS SELECT $columns FROM ".$getfromtable;
        elseif (isset($getfromtable) && $getisinto) 
			$sql="SELECT $columns INTO $table FROM ".$getfromtable;
        else 
			$sql="SELECT $columns FROM ".$table;

        if (!empty($get_args)) {
			if (is_string($get_args[0])) {
                $args_by = '';
                $groupbyset = false;      
                $havingset = false;             
                $orderbyset = false;   
				foreach ($get_args as $where_groupby_having_orderby) {
                    if (strpos($where_groupby_having_orderby,'WHERE')!==false ) {
                        $args_by .= $where_groupby_having_orderby;
                        $skipwhere = true;
                    } elseif (strpos($where_groupby_having_orderby,'GROUP BY')!==false ) {
                        $args_by .= ' '.$where_groupby_having_orderby;
                        $groupbyset = true;
                    } elseif (strpos($where_groupby_having_orderby,'HAVING')!==false ) {
                        if ($groupbyset) {
                            $args_by .= ' '.$where_groupby_having_orderby;
                            $havingset = true;
                        } else {
                            $this->setParamaters();
                            return false;
                        }
                    } elseif (strpos($where_groupby_having_orderby,'ORDER BY')!==false ) {
                        $args_by .= ' '.$where_groupby_having_orderby;    
                        $orderbyset = true;
                    }
                }
                if ($skipwhere || $groupbyset || $havingset || $orderbyset) {
                    $where = $args_by;
                    $skipwhere = true;
                }
			}		
		} else {
            $skipwhere = true;
        }        
        
        if (! $skipwhere)
            $where = $this->where( ...$wherekeys);
        
        if (is_string($where)) {
            $sql .= $where;
            if ($getselect_result) 
                return (($this->getPrepare()) && !empty($this->getParamaters())) ? $this->get_results($sql, OBJECT, true) : $this->get_results($sql);     
            else 
                return $sql;
        } else {
            $this->setParamaters();
            return false;
        }             
    }
	
    // Returns: string - sql statement from selecting method instead of executing get_result
    function select_sql($table='', $fields='*', ...$get_args) {
		$this->select_result = false;
        return $this->selecting($table, $fields, ...$get_args);	            
    }
    
	/**********************************************************************
    * desc: does an create select statement by calling selecting method
    * param: @newtable, - new database table to be created 
    *	@fromcolumns - the columns from old database table
    *	@oldtable - old database table 
    *        @wherekey, - where clause ( array(x, =, y, and, extra) ) or ( "x  =  y  and  extra" )
    *   example: where( array(key, operator, value, combine, extra) ); or where( "key operator value combine extra" );
    * returns: 
	*/
    function create_select($newtable, $fromcolumns, $oldtable=null, ...$fromwhere) {
		if (isset($oldtable))
			$this->fromtable = $oldtable;
		else {
            $this->setParamaters();
			return false;            
        }
			
        $newtablefromtable = $this->select_sql($newtable, $fromcolumns, ...$fromwhere);			
        if (is_string($newtablefromtable))
            return (($this->getPrepare()) && !empty($this->getParamaters())) ? $this->query($newtablefromtable, true) : $this->query($newtablefromtable); 
        else {
            $this->setParamaters();
            return false;    		
        }
    }
    
    /**********************************************************************
    * desc: does an select into statement by calling selecting method
    * param: @newtable, - new database table to be created 
    *	@fromcolumns - the columns from old database table
    *	@oldtable - old database table 
    *        @wherekey, - where clause ( array(x, =, y, and, extra) ) or ( "x  =  y  and  extra" )
	*   example: where( array(key, operator, value, combine, extra) ); or where( "key operator value combine extra" );
    * returns: 
	*/
    function select_into($newtable, $fromcolumns, $oldtable=null, ...$fromwhere) {
		$this->isinto = true;        
		if (isset($oldtable))
			$this->fromtable = $oldtable;
		else {
			$this->setParamaters();
            return false;          			
		}  
			
        $newtablefromtable = $this->select_sql($newtable, $fromcolumns, ...$fromwhere);
        if (is_string($newtablefromtable))
            return (($this->getPrepare()) && !empty($this->getprepared())) ? $this->query($newtablefromtable, true) : $this->query($newtablefromtable); 
        else {
			$this->setParamaters();
            return false;          			
		}  
    }
		
	/**********************************************************************
	* desc: does an update query with an array, by conditional operator array
	* param: @table, - database table to access
	*	@keyandvalue, - table fields, assoc array with key = value (doesn't need escaped)
	*   @wherekey, - where clause ( array(x, =, y, and, extra) ) or ( "x  =  y  and  extra" )
	*		example: where( array(key, operator, value, combine, extra) ); or where( "key operator value combine extra" );
	* returns: (query_id) for fetching results etc, or false for error
	*/
    function update($table='', $keyandvalue, ...$wherekeys) {        
        if ( ! is_array( $keyandvalue ) || ! isset($table) || $table=='' ) {
			$this->setParamaters();
            return false;
        }
        
        $sql="UPDATE $table SET ";
        
        foreach($keyandvalue as $key=>$val) {
            if(strtolower($val)=='null') {
				$sql.= "$key = NULL, ";
            } elseif(in_array(strtolower($val), array( 'current_timestamp()', 'date()', 'now()' ))) {
				$sql.= "$key = CURRENT_TIMESTAMP(), ";
			} else {
				if ($this->getPrepare()) {
					$sql.= "$key = "._TAG.", ";
					$this->setParamaters($val);
				} else 
					$sql.= "$key = '".$this->escape($val)."', ";
			}
        }
        
        $where = $this->where(...$wherekeys);
        if (is_string($where)) {   
            $sql = rtrim($sql, ', ') . $where;
            return (($this->getPrepare()) && !empty($this->getParamaters())) ? $this->query($sql, true) : $this->query($sql) ;       
        } else {
			$this->setParamaters();
            return false;
		}
    }   
         
	/**********************************************************************
         * desc: helper does the actual insert or replace query with an array
	*/
    function delete($table='', ...$wherekeys) {   
        if ( empty($table) ) {
			$this->setParamaters();
            return false;          			
		}  
		
        $sql="DELETE FROM $table";
        
        $where = $this->where(...$wherekeys);
        if (is_string($where)) {   
            $sql .= $where;						
            return (($this->getPrepare()) && !empty($this->getParamaters())) ? $this->query($sql, true) : $this->query($sql) ;  
        } else {
			$this->setParamaters();
            return false;          			
		}  
    }
    
	/**********************************************************************
         * desc: helper does the actual insert or replace query with an array
	*/
    function _query_insert_replace($table='', $keyandvalue, $type='', $execute=true) {  
        if ((! is_array($keyandvalue) && ($execute)) || $table=='' ) {
			$this->setParamaters();
            return false;          			
		}  
        
        if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ))) {
			$this->setParamaters();
            return false;          			
		}  
            
        $sql="$type INTO $table";
        $v=''; $n='';

        if ($execute) {
            foreach($keyandvalue as $key=>$val) {
                $n.="$key, ";
                if(strtolower($val)=='null') $v.="NULL, ";
                elseif(in_array(strtolower($val), array( 'current_timestamp()', 'date()', 'now()' ))) $v.="CURRENT_TIMESTAMP(), ";
                else  {
					if ($this->getPrepare()) {
						$v.= _TAG.", ";
						$this->setParamaters($val);
					} else 
						$v.= "'".$this->escape($val)."', ";
				}               
            }
            
            $sql .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

			if (($this->getPrepare()) && !empty($this->getParamaters())) 
				$ok = $this->query($sql, true);
			else 
				$ok = $this->query($sql);
				
            if ($ok)
                return $this->insert_id;
            else {
				$this->setParamaters();
				return false;          			
			}  
        } else {
            if (is_array($keyandvalue)) {
                if (array_keys($keyandvalue) === range(0, count($keyandvalue) - 1)) {
                    foreach($keyandvalue as $key) {
                        $n.="$key, ";                
                    }
                    $sql .= " (". rtrim($n, ', ') .") ";                         
                } else {
					return false;          			
				}          
            } 
            return $sql;
        }
	}
        
	/**********************************************************************
    * desc: does an replace query with an array
    * param: @table, - database table to access
    *		@keyandvalue - table fields, assoc array with key = value (doesn't need escaped)
    * returns: id of replaced record, or false for error
	*/
    function replace($table='', $keyandvalue) {
            return $this->_query_insert_replace($table, $keyandvalue, 'REPLACE');
        }

	/**********************************************************************
    * desc: does an insert query with an array
    * param: @table, - database table to access
    * 		@keyandvalue - table fields, assoc array with key = value (doesn't need escaped)
    * returns: id of inserted record, or false for error
	*/
    function insert($table='', $keyandvalue) {
        return $this->_query_insert_replace($table, $keyandvalue, 'INSERT');
    }
    
	/**********************************************************************
    * desc: does an insert into select statement by calling insert method helper then selecting method
    * param: @totable, - database table to insert table into 
    *		@tocolumns - the receiving columns from other table columns, leave blank for all or array of column fields
    *        @wherekey, - where clause ( array(x, =, y, and, extra) ) or ( "x = y and extra" )
    *		example: where( array(key, operator, value, combine, extra) ); or where( "key operator value combine extra" );
    * returns: 
	*/
    function insert_select($totable='', $tocolumns='*', $fromtable, $fromcolumns='*', ...$fromwhere) {
        $puttotable = $this->_query_insert_replace($totable, $tocolumns, 'INSERT', false);
        $getfromtable = $this->select_sql($fromtable, $fromcolumns, ...$fromwhere);
        if (is_string($puttotable) && is_string($getfromtable))
            return (($this->getPrepare()) && !empty($this->getParamaters())) ? $this->query($puttotable." ".$getfromtable, true) : $this->query($puttotable." ".$getfromtable) ;
        else {
			$this->setParamaters();
            return false;          			
		}                 
    }    
    
}
    
    
    
    
	class ezSQLcore extends ezQuery
	{		
    
		public $trace            = false;  // same as $debug_all
		public $debug_all        = false;  // same as $trace
		public $debug_called     = false;
		public $vardump_called   = false;
		public $show_errors      = true;
		public $num_queries      = 0;
		public $conn_queries     = 0;
		public $last_query       = null;
		public $last_error       = null;
		public $col_info         = null;
		public $captured_errors  = array();
		public $cache_dir        = false;
		public $cache_queries    = false;
		public $cache_inserts    = false;
		public $use_disk_cache   = false;
		public $cache_timeout    = 24; // hours
		public $timers           = array();
		public $total_query_time = 0;
		public $db_connect_time  = 0;
		public $trace_log        = array();
		public $use_trace_log    = false;
		public $sql_log_file     = false;
		public $do_profile       = false;
		public $profile_times    = array();
		public $insert_id        = null;
		
    /**
     * Whether the database connection is established, or not
     * @public boolean Default is false
     */
    protected $_connected = false;    
    /**
     * Contains the number of affected rows of a query
     * @public int Default is 0
     */
    protected $_affectedRows = 0;

    /**
     * The last query result
     * @public object Default is null
     */
    public $last_result = null;

    /**
     * Get data from disk cache
     * @public boolean Default is false
     */
    public $from_disk_cache = false;

    /**
     * Function called
     * @private string
     */
    private $func_call; 

	/**
     * All functions called
     * @private array 
     */
    private $all_func_calls = array();

		// == TJH == default now needed for echo of debug function
		public $debug_echo_is_on = true;

		/**********************************************************************
		*  Constructor
		*/
		function __construct()
		{
            parent::__construct();
		}

		/**********************************************************************
		*  Get host and port from an "host:port" notation.
		*  Returns array of host and port. If port is omitted, returns $default
		*/
		function get_host_port( $host, $default = false )
		{
			$port = $default;
			if ( false !== strpos( $host, ':' ) ) {
				list( $host, $port ) = explode( ':', $host );
				$port = (int) $port;
			}
			return array( $host, $port );
		}

		/**********************************************************************
		*  Print SQL/DB error - over-ridden by specific DB class
		*/
		function register_error($err_str)
		{
			// Keep track of last error
			$this->last_error = $err_str;

			// Capture all errors to an error array no matter what happens
			$this->captured_errors[] = array
			(
				'error_str' => $err_str,
				'query'     => $this->last_query
			);
		}

		/**********************************************************************
		*  Turn error handling on or off..
		*/
		function show_errors()
		{
			$this->show_errors = true;
		}

		function hide_errors()
		{
			$this->show_errors = false;
		}

		/**********************************************************************
		*  Kill cached query results
		*/
		function flush()
		{
			// Get rid of these
			$this->last_result = null;
			$this->col_info = null;
			$this->last_query = null;
			$this->from_disk_cache = false;
            $this->setParamaters();
		}

		/**********************************************************************
		* Log how the query function was called
		* @param string
		*/
		function log_query($query)
		{
			// Log how the last function was called
			$this->func_call = $query;
			
			// Keep an running Log of all functions called
			array_push($this->all_func_calls, $this->func_call);
		}

		/**********************************************************************
		* Get one variable from the DB - see docs for more detail
		*/
		function get_var($query=null,$x=0,$y=0, $use_prepare=false)
		{
			// Log how the function was called
			$this->log_query("\$db->get_var(\"$query\",$x,$y)");

			// If there is a query then perform it if not then use cached results..
			if ( $query)
			{
				$this->query($query, $use_prepare);
			}

			// Extract public out of cached results based x,y vals
			if ( $this->last_result[$y] )
			{
				$values = array_values(get_object_vars($this->last_result[$y]));
			}
			
			// If there is a value return it else return null
			return (isset($values[$x]) && $values[$x]!=='')?$values[$x]:null;
		}

		/**********************************************************************
		*  Get one row from the DB - see docs for more detail
		*/
		function get_row($query=null,$output=OBJECT,$y=0, $use_prepare=false)
		{
			// Log how the function was called
			$this->log_query("\$db->get_row(\"$query\",$output,$y)");

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query, $use_prepare);
			}

			// If the output is an object then return object using the row offset..
			if ( $output == OBJECT )
			{
				return $this->last_result[$y]?$this->last_result[$y]:null;
			}
			// If the output is an associative array then return row as such..
			elseif ( $output == ARRAY_A )
			{
				return $this->last_result[$y]?get_object_vars($this->last_result[$y]):null;
			}
			// If the output is an numerical array then return row as such..
			elseif ( $output == ARRAY_N )
			{
				return $this->last_result[$y]?array_values(get_object_vars($this->last_result[$y])):null;
			}
			// If invalid output type was specified..
			else
			{
				$this->show_errors ? trigger_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N",E_USER_WARNING) : null;
			}
		}

		/**********************************************************************
		*  Function to get 1 column from the cached result set based in X index
		*  see docs for usage and info
		*/

		function get_col($query=null,$x=0, $use_prepare=false)
		{

			$new_array = array();

			// If there is a query then perform it if not then use cached results..
			if ( $query )
			{
				$this->query($query, $use_prepare);
			}

			// Extract the column values
			$j = count($this->last_result);
			for ( $i=0; $i < $j; $i++ )
			{
				$new_array[$i] = $this->get_var(null,$x,$i);
			}

			return $new_array;
		}

		/**********************************************************************
		*  Return the the query as a result set, will use prepare statements if setup - see docs for more details
		*/
		function get_results($query=null, $output = OBJECT, $use_prepare=false) {
			// Log how the function was called
			$this->log_query("\$db->get_results(\"$query\", $output, $use_prepare)");

			// If there is a query then perform it if not then use cached results..
			if ( $query ) {
				$this->query($query, $use_prepare);
			}

			if ( $output == OBJECT ) {
				return $this->last_result;
			} elseif ( $output == _JSON ) { 
				return json_encode($this->last_result); // return as json output
			} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
				if ( $this->last_result ) {
					$i=0;
					foreach( $this->last_result as $row ) {
						$new_array[$i] = get_object_vars($row);
						if ( $output == ARRAY_N ) {
							$new_array[$i] = array_values($new_array[$i]);
						}
						$i++;
					}
					return $new_array;
				} else {
					return array();
				}
			}
		}
					
		/**********************************************************************
		*  Function to get column meta data info pertaining to the last query
		* see docs for more info and usage
		*/
		function get_col_info($info_type="name",$col_offset=-1)
		{
			if ( $this->col_info )
			{
				if ( $col_offset == -1 )
				{
					$i=0;
					foreach($this->col_info as $col )
					{
						$new_array[$i] = $col->{$info_type};
						$i++;
					}
					return $new_array;
				}
				else
				{
					return $this->col_info[$col_offset]->{$info_type};
				}
			}
		}

		/**********************************************************************
		*  store_cache
		*/
		function store_cache($query,$is_insert)
		{
			// The would be cache file for this query
			$cache_file = $this->cache_dir.'/'.md5($query);

			// disk caching of queries
			if ( $this->use_disk_cache && ( $this->cache_queries && ! $is_insert ) || ( $this->cache_inserts && $is_insert ))
			{
				if ( ! is_dir($this->cache_dir) )
				{
					$this->register_error("Could not open cache dir: $this->cache_dir");
					$this->show_errors ? trigger_error("Could not open cache dir: $this->cache_dir",E_USER_WARNING) : null;
				}
				else
				{
					// Cache all result values
					$result_cache = array
					(
						'col_info' => $this->col_info,
						'last_result' => $this->last_result,
						'num_rows' => $this->num_rows,
						'return_value' => $this->num_rows,
					);
					file_put_contents($cache_file, serialize($result_cache));
					if( file_exists($cache_file . ".updating") )
						unlink($cache_file . ".updating");
				}
			}
		}

		/**********************************************************************
		*  get_cache
		*/
		function get_cache($query)
		{
			// The would be cache file for this query
			$cache_file = $this->cache_dir.'/'.md5($query);

			// Try to get previously cached version
			if ( $this->use_disk_cache && file_exists($cache_file) )
			{
				// Only use this cache file if less than 'cache_timeout' (hours)
				if ( (time() - filemtime($cache_file)) > ($this->cache_timeout*3600) &&
					!(file_exists($cache_file . ".updating") && (time() - filemtime($cache_file . ".updating") < 60)) )
				{
					touch($cache_file . ".updating"); // Show that we in the process of updating the cache
				}
				else
				{
					$result_cache = unserialize(file_get_contents($cache_file));

					$this->col_info = $result_cache['col_info'];
					$this->last_result = $result_cache['last_result'];
					$this->num_rows = $result_cache['num_rows'];

					$this->from_disk_cache = true;

					// If debug ALL queries
					$this->trace || $this->debug_all ? $this->debug() : null ;

					return $result_cache['return_value'];
				}
			}
		}

		/**********************************************************************
		*  Dumps the contents of any input variable to screen in a nicely
		*  formatted and easy to understand way - any type: Object, public or Array
		*/
		function vardump($mixed='')
		{
			// Start output buffering
			ob_start();

			echo "<p><table><tr><td bgcolor=ffffff><blockquote><font color=000090>";
			echo "<pre><font face=arial>";

			if ( ! $this->vardump_called )
			{
				echo "<font color=800080><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Variable Dump..</b></font>\n\n";
			}

			$var_type = gettype ($mixed);
			print_r(($mixed?$mixed:"<font color=red>No Value / False</font>"));
			echo "\n\n<b>Type:</b> " . ucfirst($var_type) . "\n";
			echo "<b>Last Query</b> [$this->num_queries]<b>:</b> ".($this->last_query?$this->last_query:"NULL")."\n";
			echo "<b>Last Function Call:</b> " . ($this->func_call?$this->func_call:"None")."\n";
			
			if (count($this->all_func_calls)>1)
			{
				echo "<b>List of All Function Calls:</b><br>"; 
				foreach($this->all_func_calls as $func_string)
					echo "  " . $func_string ."<br>\n";
			}
			
			echo "<b>Last Rows Returned:</b> ".(count($this->last_result)>0 ? $this->last_result : '')."\n";
			echo "</font></pre></font></blockquote></td></tr></table>".$this->donation();
			echo "\n<hr size=1 noshade color=dddddd>";

			// Stop output buffering and capture debug HTML
			$html = ob_get_contents();
			ob_end_clean();

			// Only echo output if it is turned on
			if ( $this->debug_echo_is_on )
			{
				echo $html;
			}

			$this->vardump_called = true;			
			return $html;
		}

		/**********************************************************************
		*  Alias for the above function
		*/
		function dumpvar($mixed)
		{
			return $this->vardump($mixed);
		}

		/**********************************************************************
		*  Displays the last query string that was sent to the database & a
		* table listing results (if there were any).
		* (abstracted into a seperate file to save server overhead).
		*/
		function debug($print_to_screen=true)
		{
			// Start outup buffering
			ob_start();

			echo "<blockquote>";

			// Only show ezSQL credits once..
			if ( ! $this->debug_called )
			{
				echo "<font color=800080 face=arial size=2><b>ezSQL</b> (v".EZSQL_VERSION.") <b>Debug..</b></font><p>\n";
			}

			if ( $this->last_error )
			{
				echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>$this->last_error</b></font>]<p>";
			}

			if ( $this->from_disk_cache )
			{
				echo "<font face=arial size=2 color=000099><b>Results retrieved from disk cache</b></font><p>";
			}

			echo "<font face=arial size=2 color=000099><b>Query</b> [$this->num_queries] <b>--</b> ";
			echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";
			echo "<font face=arial size=2 color=000099><b>Query Result..</b></font>";
			echo "<blockquote>";

			if ( $this->col_info )
			{
				// =====================================================
				// Results top rows
				echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
				echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";

				for ( $i=0, $j=count($this->col_info); $i < $j; $i++ )
				{
					/* when selecting count(*) the maxlengh is not set, size is set instead. */
					echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$this->col_info[$i]->type}";
					if (!isset($this->col_info[$i]->max_length))
					{
						echo "{$this->col_info[$i]->size}";
					} else {
						echo "{$this->col_info[$i]->max_length}";
					}
					echo "</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$this->col_info[$i]->name}</span></td>";
				}
				echo "</tr>";

				// ======================================================
				// print main results
				if ( $this->last_result )
				{
					$i=0;
					foreach ( $this->get_results(null,ARRAY_N) as $one_row )
					{
						$i++;
						echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

						foreach ( $one_row as $item )
						{
							echo "<td nowrap><font face=arial size=2>$item</font></td>";
						}
						echo "</tr>";
					}
				// if last result
				} else {
					echo "<tr bgcolor=ffffff><td colspan=".(count($this->col_info)+1)."><font face=arial size=2>No Results</font></td></tr>";
				}
				echo "</table>";
			// if col_info
			} else {
				echo "<font face=arial size=2>No Results</font>";
			}

			echo "</blockquote></blockquote>".$this->donation()."<hr noshade color=dddddd size=1>";

			// Stop output buffering and capture debug HTML
			$html = ob_get_contents();
			ob_end_clean();

			// Only echo output if it is turned on
			if ( $this->debug_echo_is_on && $print_to_screen)
			{
				echo $html;
			}

			$this->debug_called = true;
			return $html;
		}

		/**********************************************************************
		*  Naughty little function to ask for some remuniration!
		*/
		function donation()
		{
			return "<font size=1 face=arial color=000000>If ezSQL has helped <a href=\"https://www.paypal.com/xclick/business=justin%40justinvincent.com&item_name=ezSQL&no_note=1&tax=0\" style=\"color: 0000CC;\">make a donation!?</a> &nbsp;&nbsp;<!--[ go on! you know you want to! ]--></font>";
		}

		/**********************************************************************
		*  Timer related functions
		*/
		function timer_get_cur()
		{
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}

		function timer_start($timer_name)
		{
			$this->timers[$timer_name] = $this->timer_get_cur();
		}

		function timer_elapsed($timer_name)
		{
			return round($this->timer_get_cur() - $this->timers[$timer_name],2);
		}

		function timer_update_global($timer_name)
		{
			if ( $this->do_profile )
			{
				$this->profile_times[] = array
				(
					'query' => $this->last_query,
					'time' => $this->timer_elapsed($timer_name)
				);
			}
			$this->total_query_time += $this->timer_elapsed($timer_name);
		}

		/**********************************************************************
		* Creates a SET nvp sql string from an associative array (and escapes all values)
		*
		*  Usage:
		*
		*     $db_data = array('login'=>'jv','email'=>'jv@vip.ie', 'user_id' => 1, 'created' => 'NOW()');
		*
		*     $db->query("INSERT INTO users SET ".$db->get_set($db_data));
		*
		*     ...OR...
		*
		*     $db->query("UPDATE users SET ".$db->get_set($db_data)." WHERE user_id = 1");
		*
		* Output:
		*
		*     login = 'jv', email = 'jv@vip.ie', user_id = 1, created = NOW()
		*/
		function get_set($params)
		{
			if( !is_array( $params ) )
			{
				$this->register_error( 'get_set() parameter invalid. Expected array in '.__FILE__.' on line '.__LINE__);
				return;
			}
			$sql = array();
			foreach ( $params as $field => $val )
			{
				if ( $val === 'true' || $val === true )
					$val = 1;
				if ( $val === 'false' || $val === false )
					$val = 0;

				switch( $val ){
					case 'NOW()' :
					case 'NULL' :
					  $sql[] = "$field = $val";
						break;
					default :
						$sql[] = "$field = '".$this->escape( $val )."'";
				}
			}
			return implode( ', ' , $sql );
		}

		/**
		 * Function for operating query count
		 *
		 * @param bool $all Set to false for function to return queries only during this connection
		 * @param bool $increase Set to true to increase query count (internal usage)
		 * @return int Returns query count base on $all
		 */
		function count ($all = true, $increase = false) {
			if ($increase) {
				$this->num_queries++;
				$this->conn_queries++;
			}

			return ($all) ? $this->num_queries : $this->conn_queries;
		}

    /**
     * Returns, whether a database connection is established, or not
     *
     * @return boolean
     */
    function isConnected() {
        return $this->_connected;
    } // isConnected

    /**
     * Returns the current show error state
     *
     * @return boolean
     */
    function getShowErrors() {
        return $this->show_errors;
    } // getShowErrors

    /**
     * Returns the affected rows of a query
     * 
     * @return int
     */
    function affectedRows() {
        return $this->_affectedRows;
    } // affectedRows
	
	// query call template
    function query($query, $use_prepare=false) {
		return false;
	}    
	
	// escape call template if not available by vendor
	function escape($data) {
		if ( !isset($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
                '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
                '/%1[0-9a-f]/',             // url encoded 16-31
                '/[\x00-\x08]/',            // 00-08
                '/\x0b/',                   // 11
                '/\x0c/',                   // 12
                '/[\x0e-\x1f]/'             // 14-31
                );
                
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $data);
	}
        
} // ezSQLcore
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*#################################################################################################################################################*/




	/**********************************************************************
	*  Author: Justin Vincent (jv@jvmultimedia.com) / Silvio Wanka 
	* Contributor:  Lawrence Stubbs <technoexpressnet@gmail.com>
	*  Web...: http://twitter.com/justinvincent
	*  Name..: ezSQL_sqlite3
	*  Desc..: SQLite3 component (part of ezSQL databse abstraction library)
	*
	*/

	/**********************************************************************
	*  ezSQL error strings - SQLite
	*/

	global $ezsql_sqlite3_str;
	
	$ezsql_sqlite3_str = array
	(
		1 => 'Require $dbpath and $dbname to open an SQLite database'
	);

	/**********************************************************************
	*  ezSQL Database specific class - SQLite
	*/

	if ( ! class_exists ('SQLite3') ) die('<b>Fatal Error:</b> ezSQL_sqlite3 requires SQLite3 Lib to be compiled and or linked in to the PHP engine');
	if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_sqlite3 requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

	class ezSQL_sqlite3 extends ezSQLcore
	{

		private $rows_affected = false;
        
		protected $preparedvalues = array();

		/**********************************************************************
		*  Constructor - allow the user to perform a quick connect at the 
		*  same time as initializing the ezSQL_sqlite3 class
		*/

		function __construct($dbpath='', $dbname='')
		{
            parent::__construct();
			// Turn on track errors 
			ini_set('track_errors',1);
			
			if ( $dbpath && $dbname )
			{
				$this->connect($dbpath, $dbname);
			}
            
            global $_ezSqlite3;
            $_ezSqlite3 = $this;
		}

		/**********************************************************************
		*  Try to connect to SQLite database server
		*/

		function connect($dbpath='', $dbname='')
		{
			global $ezsql_sqlite3_str; 
            $return_val = false;
            $this->_connected = false;
			
			// Must have a user and a password
			if ( ! $dbpath || ! $dbname )
			{
				$this->register_error($ezsql_sqlite3_str[1].' in '.__FILE__.' on line '.__LINE__);
				$this->show_errors ? trigger_error($ezsql_sqlite3_str[1],E_USER_WARNING) : null;
				return false;
			}
			// Try to establish the server database handle
			else if ( ! $this->dbh = @new SQLite3($dbpath.$dbname) )
			{
				$this->register_error($php_errormsg);
				$this->show_errors ? trigger_error($php_errormsg,E_USER_WARNING) : null;
				return false;
			}
			else
			{
				$return_val = true;
				$this->conn_queries = 0;
                $this->_connected = true;
			}

			return $return_val;			
		}

		/**********************************************************************
		*  In the case of SQLite quick_connect is not really needed
		*  because std. connect already does what quick connect does - 
		*  but for the sake of consistency it has been included
		*/

		function quick_connect($dbpath='', $dbname='')
		{
			return $this->connect($dbpath, $dbname);
		}

		/**********************************************************************
		*  Format a SQLite string correctly for safe SQLite insert
		*  (no mater if magic quotes are on or not)
		*/

		function escape($str)
		{
			return $this->dbh->escapeString(stripslashes(preg_replace("/[\r\n]/",'',$str)));				
		}

		/**********************************************************************
		*  Return SQLite specific system date syntax 
		*  i.e. Oracle: SYSDATE Mysql: NOW()
		*/

		function sysdate()
		{
			return 'now';			
		}
        
        // Get the data type of the value to bind. 
        function getArgType($arg) {
            switch (gettype($arg)) {
                case 'double':  return SQLITE3_FLOAT;
                case 'integer': return SQLITE3_INTEGER;
                case 'boolean': return SQLITE3_INTEGER;
                case 'NULL':    return SQLITE3_NULL;
                case 'string':  return SQLITE3_TEXT;
                case 'string':  return SQLITE3_TEXT;
                default: 
                    $type_error = 'Argument is of invalid type '.gettype($arg);
                    $this->register_error($type_error);
                    $this->show_errors ? trigger_error($type_error,E_USER_WARNING) : null;
                    return false;
            }
        }
        
        /**
		* Creates a prepared query, binds the given parameters and returns the result of the executed
		* @param string $query
		* @param array $param
		* @return bool \SQLite3Result 
		*/
        function query_prepared($query, $param=null)
        { 
            $stmt = $this->dbh->prepare($query);
            foreach ($param as $index => $val) {
                // indexing start from 1 in Sqlite3 statement
                if (is_array($val)) {
                    $ok = $stmt->bindParam($index + 1, $val);
                } else {
                    $ok = $stmt->bindValue($index + 1, $val, $this->getArgType($val));
                }
               
                if (!$ok) {
                    $type_error = "Unable to bind param: $val";
                    $this->register_error($type_error);
                    $this->show_errors ? trigger_error($type_error,E_USER_WARNING) : null;
                    return false;
                }
            }
            
            return $stmt->execute();
        }
    
		/**********************************************************************
		*  Perform SQLite query and try to determine result value
		*/

		// ==================================================================
		//	Basic Query	- see docs for more detail
	
		function query($query, $use_prepare=false)
        {
            if ($use_prepare)
                $param = &$this->getParamaters();
            
			// check for ezQuery placeholder tag and replace tags with proper prepare tag
			$query = str_replace(_TAG, '?', $query);
            
			// For reg expressions
			$query = str_replace("/[\n\r]/",'',trim($query)); 

			// initialize return
			$return_val = 0;

			// Flush cached values..
			$this->flush();

			// Log how the function was called
			$this->log_query("\$db->query(\"$query\")");

			// Keep track of the last query for debug..
			$this->last_query = $query;

			// Perform the query via std SQLite3 query or SQLite3 prepare function..
            if (!empty($param) && is_array($param) && ($this->getPrepare())) {
                $this->result = $this->query_prepared($query, $param);	
				$this->setParamaters();
            } else 
                $this->result = $this->dbh->query($query);
			$this->count(true, true);

			// If there is an error then take note of it..
			if (@$this->dbh->lastErrorCode())
			{
				$err_str = $this->dbh->lastErrorMsg();
				$this->register_error($err_str);
				$this->show_errors ? trigger_error($err_str,E_USER_WARNING) : null;
				return false;
			}
			
			// Query was an insert, delete, update, replace
			if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
			{
				$this->rows_affected = @$this->dbh->changes();
				
				// Take note of the insert_id
				if ( preg_match("/^(insert|replace)\s+/i",$query) )
				{
					$this->insert_id = @$this->dbh->lastInsertRowID();	
				}
				
				// Return number of rows affected
				$return_val = $this->rows_affected;
	
			}
			// Query was an select
			else
			{
				
				// Take note of column info	
				$i=0;
				$this->col_info = array();
				while ($i < @$this->result->numColumns())
				{
					$this->col_info[$i] = new StdClass;
					$this->col_info[$i]->name       = $this->result->columnName($i);
					$this->col_info[$i]->type       = null;
					$this->col_info[$i]->max_length = null;
					$i++;
				}
				
				// Store Query Results
				$num_rows=0;
				while ($row =  @$this->result->fetchArray(SQLITE3_ASSOC))
				{
					// Store result as an objects within main array
					$obj= (object) $row; //convert to object
					$this->last_result[$num_rows] = $obj;
					$num_rows++;
				}
                

				// Log number of rows the query returned
				$this->num_rows = $num_rows;
				
				// Return number of rows selected
				$return_val = $this->num_rows;
			
			}
            
            //if (($param) && is_array($param) && ($this->getPrepare()))
             //   $this->result->finalize(); 

			// If debug ALL queries
			$this->trace||$this->debug_all ? $this->debug() : null ;

			return $return_val;
		
		}

	}
?>