<?php

function sysToolsJavaModuleGeneratorGo()
{

    $module = $_REQUEST['module'];
    $attributes = explode(',', $_REQUEST['attributes']);

    $attributeArray = array();
    foreach ($attributes as $attribute) {
        $attributeArray[] = explode(":", $attribute);
    }
    $attributes = $attributeArray;


    $sysNameLowerCase = strtolower($module);
    $sysNameFirstLetterLowercase = $module;
    $sysNameCamelCase = ucfirst($module);

    $class_sysNameCamelCase = null;
    $class_sysNameCamelCase_Controller = null;
    $class_sysNameCamelCase_Dto = null;
    $class_sysNameCamelCase_Mapper = null;
    $class_sysNameCamelCase_NotFoundException = null;
    $class_sysNameCamelCase_Repository = null;
    $class_sysNameCamelCase_Service = null;

    $class_sysNameCamelCase_Service .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

@Slf4j
@Service
public class ' . $sysNameCamelCase . 'Service {

	private final ' . $sysNameCamelCase . 'Repository repository;

	@Autowired
	public ' . $sysNameCamelCase . 'Service(' . $sysNameCamelCase . 'Repository repository) {
		this.repository = repository;
	}

	public ' . $sysNameCamelCase . 'Model findById(Long id) throws ' . $sysNameCamelCase . 'NotFoundException {
		return repository.findById(id).orElseThrow(() -> new ' . $sysNameCamelCase . 'NotFoundException(String.format("No data found for given id: %s", id)));
	}

	public List<' . $sysNameCamelCase . 'Model> getAll() {
		return repository.findAll();
	}

	public ' . $sysNameCamelCase . 'Model save(' . $sysNameCamelCase . 'Model model) {
		return repository.save(model);
	}

	public void delete(Long id) throws ' . $sysNameCamelCase . 'NotFoundException {
		' . $sysNameCamelCase . 'Model model = repository.findById(id).orElseThrow(() -> new ' . $sysNameCamelCase . 'NotFoundException(String.format("No data found for given id: %s", id)));
		repository.delete(model);
	}

	public ' . $sysNameCamelCase . 'Model updateById(Long id, ' . $sysNameCamelCase . 'Model modelData) throws ' . $sysNameCamelCase . 'NotFoundException {

		' . $sysNameCamelCase . 'Model model = repository.findById(id).orElseThrow(() -> new ' . $sysNameCamelCase . 'NotFoundException(String.format("No data found for given id: %s", id)));
		 
		
';

    foreach ($attributes as $attribute) {
        $name = $attribute[0];
        $id = $attribute[1];
        $type = sysModuleGetRows(false, 'sysModuleAttributeType', $id, 'id');
        $type = array_pop($type);
        $type = $type['name'];

        if ($type == 'timestamp') {
            $class_sysNameCamelCase_Service .= '';
        } else {
            $class_sysNameCamelCase_Service .= '
			model.set' . ucfirst($name) . '(modelData.get' . ucfirst($name) . '());';
        }
    }

    $class_sysNameCamelCase_Service .= '
		
		repository.save(model);
		return model;
	}
}';

    $class_sysNameCamelCase_Repository .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface ' . $sysNameCamelCase . 'Repository extends JpaRepository<' . $sysNameCamelCase . 'Model, Long> {

}';


    $class_sysNameCamelCase_NotFoundException .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import com.devops.api.exception.ApiRestException;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.ResponseStatus;

@ResponseStatus(value = HttpStatus.NOT_FOUND)
public class ' . $sysNameCamelCase . 'NotFoundException extends ApiRestException {

	public ' . $sysNameCamelCase . 'NotFoundException(String message) {
		super(message);
	}

}';


    $class_sysNameCamelCase_Mapper .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import ma.glasnost.orika.CustomMapper;
import ma.glasnost.orika.MapperFactory;
import ma.glasnost.orika.MappingContext;
import ma.glasnost.orika.impl.ConfigurableMapper;
import org.springframework.stereotype.Service;

import java.util.Date;

@Service
public class ' . $sysNameCamelCase . 'Mapper extends ConfigurableMapper {

	@Override
	protected void configure(MapperFactory factory) {
		factory.classMap(' . $sysNameCamelCase . 'Model.class, ' . $sysNameCamelCase . 'Dto.class)

				.customize(new CustomMapper<>() {
					@Override
					public void mapAtoB(' . $sysNameCamelCase . 'Model a, ' . $sysNameCamelCase . 'Dto b, MappingContext context) {
';

    foreach ($attributes as $attribute) {
        $name = $attribute[0];
        $id = $attribute[1];
        $type = sysModuleGetRows(false, 'sysModuleAttributeType', $id, 'id');
        $type = array_pop($type);
        $type = $type['name'];

        if ($type == 'timestamp') {
            $class_sysNameCamelCase_Mapper .= '';
        } else {
            $class_sysNameCamelCase_Mapper .= '
			b.set' . ucfirst($name) . '(a.get' . ucfirst($name) . '());';
        }
    }

    $class_sysNameCamelCase_Mapper .= '
					}

					@Override
					public void mapBtoA(' . $sysNameCamelCase . 'Dto b, ' . $sysNameCamelCase . 'Model a, MappingContext context) {
						';

    foreach ($attributes as $attribute) {
        $name = $attribute[0];
        $id = $attribute[1];
        $type = sysModuleGetRows(false, 'sysModuleAttributeType', $id, 'id');
        $type = array_pop($type);
        $type = $type['name'];

        if ($type == 'timestamp') {
            $class_sysNameCamelCase_Mapper .= '
            Date date = new Date();
			a.setTimestamp(date);';
        } else {
            $class_sysNameCamelCase_Mapper .= '
			a.set' . ucfirst($name) . '(b.get' . ucfirst($name) . '());';
        }
    }

    $class_sysNameCamelCase_Mapper .= '
    
					}
				})
				.byDefault().register();
	}

}';

    $class_sysNameCamelCase_Dto .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import com.fasterxml.jackson.annotation.JsonProperty;
import io.swagger.annotations.ApiModelProperty;
import lombok.Data;
import org.hibernate.validator.constraints.Length;
import javax.validation.constraints.NotNull;

@Data
public class ' . $sysNameCamelCase . 'Dto {

	@ApiModelProperty(accessMode = ApiModelProperty.AccessMode.READ_ONLY, notes = "ID ")
	@JsonProperty(access = JsonProperty.Access.READ_ONLY)
	private String id;

';

    foreach ($attributes as $attribute) {
        $name = $attribute[0];
        $id = $attribute[1];
        $type = sysModuleGetRows(false, 'sysModuleAttributeType', $id, 'id');
        $type = array_pop($type);
        $type = $type['name'];

        if ($type == 'int') {
            $class_sysNameCamelCase_Dto .= '
			@ApiModelProperty(required = true, notes = "' . $name . '", example = "123")
			@NotNull
			private Long ' . $name . ';
		';
        } else if ($type == 'timestamp') {
            $class_sysNameCamelCase_Dto .= '';
        } else {
            $class_sysNameCamelCase_Dto .= '
			@ApiModelProperty(required = true, notes = "' . $name . '", example = "Foo Bar")
			@Length(max = 128)
			private String ' . $name . ';
		';
        }
    }

    $class_sysNameCamelCase_Dto .= '
}';

    $class_sysNameCamelCase_Controller .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import com.devops.api.exception.ApiRestException;
import io.swagger.annotations.ApiOperation;
import io.swagger.annotations.ApiParam;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.validation.Valid;
import java.util.ArrayList;
import java.util.List;

@Slf4j
@RestController
@RequestMapping(value = "api/' . $sysNameLowerCase . '", produces = "application/json")
public class ' . $sysNameCamelCase . 'Controller {

	private final ' . $sysNameCamelCase . 'Mapper mapper;
	private final ' . $sysNameCamelCase . 'Service service;

	@Autowired
	public ' . $sysNameCamelCase . 'Controller(' . $sysNameCamelCase . 'Mapper mapper, ' . $sysNameCamelCase . 'Service service) {
		this.mapper = mapper;
		this.service = service;
	}

	@ApiOperation(value = "Get list of items", response = ' . $sysNameCamelCase . 'Dto.class)
	@GetMapping("/list")
	public ResponseEntity<List<' . $sysNameCamelCase . 'Dto>> getAll' . $sysNameCamelCase . '() {
		List<' . $sysNameCamelCase . 'Model> itemList = service.getAll();
		List<' . $sysNameCamelCase . 'Dto> itemDtoList = mapper.mapAsList(itemList, ' . $sysNameCamelCase . 'Dto.class);

		return new ResponseEntity<>(itemDtoList, HttpStatus.OK);
	}

	@ApiOperation(value = "Get item", response = ' . $sysNameCamelCase . 'Dto.class)
	@GetMapping("/{id}")
	public ResponseEntity<' . $sysNameCamelCase . 'Dto> get' . $sysNameCamelCase . '(@ApiParam(value = "ID of item", example = "1", required = true)
																	 @PathVariable Long id) throws ApiRestException {

		' . $sysNameCamelCase . 'Model item = service.findById(id);
		' . $sysNameCamelCase . 'Dto itemDto = mapper.map(item, ' . $sysNameCamelCase . 'Dto.class);

		return new ResponseEntity<>(itemDto, HttpStatus.OK);
	}

	@ApiOperation(value = "Create item", response = ' . $sysNameCamelCase . 'Dto.class)
	@PostMapping
	public ResponseEntity<' . $sysNameCamelCase . 'Dto> create' . $sysNameCamelCase . '(@ApiParam(value = "model", required = true)
																		@RequestBody @Valid ' . $sysNameCamelCase . 'Dto dto) throws ApiRestException {

		' . $sysNameCamelCase . 'Model item = mapper.map(dto, ' . $sysNameCamelCase . 'Model.class);
		' . $sysNameCamelCase . 'Dto itemDto = mapper.map(service.save(item), ' . $sysNameCamelCase . 'Dto.class);

		return new ResponseEntity<>(itemDto, HttpStatus.CREATED);
	}

	@ApiOperation(value = "Create list items", response = ' . $sysNameCamelCase . 'Dto.class)
	@PostMapping("/list")
	public ResponseEntity<List<' . $sysNameCamelCase . 'Dto>> create' . $sysNameCamelCase . 'List(@ApiParam(value = "List of models", required = true)
																				  @RequestBody @Valid List<' . $sysNameCamelCase . 'Dto> dtoList) throws ApiRestException {

		List<' . $sysNameCamelCase . 'Dto> itemDtoList = new ArrayList<>();

		dtoList.forEach(dto -> {
			' . $sysNameCamelCase . 'Model item = mapper.map(dto, ' . $sysNameCamelCase . 'Model.class);
			' . $sysNameCamelCase . 'Dto itemDto = mapper.map(service.save(item), ' . $sysNameCamelCase . 'Dto.class);
			itemDtoList.add(itemDto);
		});

		return new ResponseEntity<>(itemDtoList, HttpStatus.CREATED);
	}

	@ApiOperation(value = "Update item given a model", response = ' . $sysNameCamelCase . 'Dto.class)
	@PutMapping("/{id}")
	public ResponseEntity<' . $sysNameCamelCase . 'Dto> update' . $sysNameCamelCase . '(@ApiParam(value = "ID of item", example = "1", required = true)
																		@PathVariable Long id,
																		@ApiParam(value = "Updated model", required = true)
																		@RequestBody @Valid ' . $sysNameCamelCase . 'Dto dto) throws ApiRestException {

		' . $sysNameCamelCase . 'Model ' . $sysNameFirstLetterLowercase . ' = mapper.map(dto, ' . $sysNameCamelCase . 'Model.class);
		' . $sysNameCamelCase . 'Dto updated' . $sysNameCamelCase . 'Dto = mapper.map(service.updateById(id, ' . $sysNameFirstLetterLowercase . '), ' . $sysNameCamelCase . 'Dto.class);

		return new ResponseEntity<>(updated' . $sysNameCamelCase . 'Dto, HttpStatus.OK);
	}

	@ApiOperation(value = "Delete item by given ID")
	@DeleteMapping("/{id}")
	public ResponseEntity<?> delete' . $sysNameCamelCase . '(@ApiParam(value = "ID of model", example = "1", required = true)
													 @PathVariable Long id) throws ApiRestException {

		service.delete(id);
		return new ResponseEntity<>(HttpStatus.OK);
	}

}';

    $class_sysNameCamelCase .= 'package com.devops.api.web.' . $sysNameLowerCase . ';

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.validator.constraints.Length;
import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.jpa.domain.support.AuditingEntityListener;

import javax.persistence.*;
import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import java.io.Serializable;
import java.util.Date;

@Data
@Entity
@Table(name = "' . $sysNameFirstLetterLowercase . '")
@EntityListeners(AuditingEntityListener.class)
@AllArgsConstructor
@NoArgsConstructor

public class ' . $sysNameCamelCase . 'Model implements Serializable {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	private Long id;
';


    foreach ($attributes as $attribute) {
        $name = $attribute[0];
        $id = $attribute[1];
        $type = sysModuleGetRows(false, 'sysModuleAttributeType', $id, 'id');
        $type = array_pop($type);
        $type = $type['name'];

        if ($type == 'int') {
            $class_sysNameCamelCase .= '
			@NotNull
			private Long ' . $name . ';
		';
        } else if ($type == 'timestamp') {
            $class_sysNameCamelCase .= '
            @Column(nullable = false, updatable = false)
			@Temporal(TemporalType.TIMESTAMP)
			@CreatedDate
			private Date ' . $name . ';
            ';
        } else {
            $class_sysNameCamelCase .= '
			@Length(max = 128)
			@NotBlank
			private String ' . $name . ';
		';
        }
    }


    $class_sysNameCamelCase .= '
}';

    $dir = UPLOADS . '/' . $sysNameLowerCase . '/';
    $zip = $sysNameLowerCase.'.zip';

    mkdir($dir, 0777, true);
    file_put_contents($dir . $sysNameCamelCase . 'Model.java', $class_sysNameCamelCase);
    file_put_contents($dir . $sysNameCamelCase . 'Controller.java', $class_sysNameCamelCase_Controller);
    file_put_contents($dir . $sysNameCamelCase . 'Dto.java', $class_sysNameCamelCase_Dto);
    file_put_contents($dir . $sysNameCamelCase . 'Mapper.java', $class_sysNameCamelCase_Mapper);
    file_put_contents($dir . $sysNameCamelCase . 'NotFoundException.java', $class_sysNameCamelCase_NotFoundException);
    file_put_contents($dir . $sysNameCamelCase . 'Repository.java', $class_sysNameCamelCase_Repository);
    file_put_contents($dir . $sysNameCamelCase . 'Service.java', $class_sysNameCamelCase_Service);

    zipMaker($dir, UPLOADS, $zip);
    cleanupDir($dir);
    rmdir($dir);
    $data =  file_get_contents(UPLOADS.'/'.$zip);
    unlink(UPLOADS.'/'.$zip);
    echo base64_encode($data);
}

function sysToolsJavaModelGenerator()
{

    echo ' < script><!--
$(function () {
    $("#go") . click(function () {
        $(document . body) . css({\'cursor\' : \'wait\'});
                    var formData = new FormData();
                    formData.append(\'data\', $("#input").val());
                        $.ajax({
    						url: ("?a=sysToolsJavaModelGeneratorGo"),
    						type: "POST",						  
    			  			data : formData,	  
           					processData: false,
           					contentType: false,
    					  	context: document.body					 
						}).done(function(data) {
                            $(document.body).css({\'cursor\' : \'default\'});
                            console.log(data);
							$("#output").html(data);
							CodeMirror.fromTextArea(document.getElementById("output"), {                              
                              lineNumbers: true,
                              mode: "text/x-java",
                              matchBrackets: true,
                              theme: "monokai"
                            });
						});
				});
			});
			--></script>';

    sysPrintBlockHeader(12, 'Java Model generator');

    echo ' 
            <textarea id="input" class="form-control" rows="12" placeholder="Enter ..."></textarea>
            
            <br />
            <a id="go" class="btn btn-secondary">Go</a>
            <br />
            <br />
            <textarea id="output" class="form-control" rows="12" placeholder="Click Go ..."></textarea>
            
           ';

    sysPrintBlockFooter();

}

function sysToolsJavaModelGeneratorGo()
{
    $text = $_REQUEST['data'];

    $modelFile = 'package com.homerbee.middleware.prso.model;

import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.annotation.JsonProperty;
import com.fasterxml.jackson.annotation.JsonPropertyOrder;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.validator.constraints.Length;

import javax.validation.constraints.NotEmpty;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Pattern;

@Data
@AllArgsConstructor
@NoArgsConstructor
@JsonPropertyOrder({';

    $modelClassName = strip_tags(substr($text, 0, strpos($text, '</h3>')));
    $modelElements = array();
    $modelColumns = array();

    //get column names
    foreach (explode('</th>', (substr($text, (strpos($text, '<th')), (strpos($text, '</thead>') - strpos($text, '<th'))))) as $columnName) {
        //if (strlen($columnName) < 10) continue;
        $modelColumns[] = removeNewlines(strip_tags($columnName));
    }

    //get lines
    foreach (explode('<tr>', substr($text, strpos($text, '<tbody>') + 7)) as $keyLine => $modelLine) {

        //go per line attributes values
        $modelLineArr = explode('</td>', $modelLine);
        foreach ($modelColumns as $key => $modelColumn) {
            //if (strlen(trim($modelColumn)) == 0) continue;
            if (isset($modelLineArr[$key])) {
                $modelElements[$keyLine][$modelColumn] = removeNewlines(strip_tags($modelLineArr[$key]));
            }
        }
    }
    //first is blank due to substr html functionality
    array_shift($modelElements);

    //elements list at beginning class
    foreach ($modelElements as $modelElement) {
        $modelFile .= '"' . $modelElement['Name'] . '",';
    }
    $modelFile = substr($modelFile, 0, -1);

    $modelFile .= "})\r\n\r\npublic class ";

    $modelFile .= "$modelClassName {\r\n\r\n\tprivate Long id;\r\n";

    //elements itself
    foreach ($modelElements as $modelElement) {
        $prestaName = $modelElement['Name'];
        $javaName = underScoreToCamelCase($prestaName);

        //@JsonProperty
        $modelFile .= ($javaName == $prestaName ? '' : "\r\n\t@JsonProperty(\"$prestaName\")");

        //@NotEmpty
        $isEmpty = in_array('Required', $modelColumns) && strpos($modelElement['Required'], '✔️') > -1;
        $modelFile .= $isEmpty ? "\r\n\t@NotNull\r\n\t@NotEmpty" : "";

        //@MaxSize
        if (in_array('Max size', $modelColumns) && is_numeric($modelElement['Max size'])) {
            $modelFile .= "\r\n\t@Length(max = " . $modelElement['Max size'] . ")";
        }

        //if type contains postcode
        if (strpos($modelElement['Name'], 'postcode') > -1) {
            $modelFile .= "\r\n\t@Pattern(regexp = \".*(^\\\\d{3}\\\\s\\\\d{2}$)\")";
        }

        //if type contains phone
        if (strpos($modelElement['Name'], 'phone') > -1) {
            $modelFile .= "\r\n\t@Pattern(regexp = \".*(^\\\\+[0-9]{2}|^\\\\+[0-9]{2}\\\\(0\\\\)|^\\\\(\\\\+[0-9]{2}\\\\)\\\\(0\\\\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\\\\-\\\\s]{10}$)\")";
        }

        //if type contains date
        if (strpos($modelElement['Name'], 'date') > -1) {
            $modelFile .= "\r\n\t@JsonFormat(pattern = \"yyyy-MM-dd HH:mm:ss\")";
        }

        //var
        $modelFile .= "\r\n\tprivate String $javaName;\r\n";
    }

    $modelFile .= "\r\n}";

    echo $modelFile;
}
